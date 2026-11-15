<?php

namespace Tests\Feature\Profiles;

use App\Kernel\ProfileInstaller;
use App\Kernel\Reconciler;
use App\Models\InstalledModule;
use App\Models\ModuleReference;
use App\Models\Role;
use App\Models\RoleCapabilityGrant;
use Moduloom\Notes\Models\Note;
use Moduloom\Tasks\Models\Task;
use Moduloom\Website\Models\WebsitePage;
use Tests\TestCase;

/**
 * Phase 3's acceptance criterion, from MASTER_ROADMAP.md, and the one
 * that validates MASTER_VISION.md's actual thesis, not just the
 * architecture: install Personal, add content, install Small Business
 * on top, verify every piece of Personal content is untouched, then
 * remove Small Business and verify Personal still works.
 */
class EvolutionTest extends TestCase
{
    public function test_small_business_installs_on_top_of_personal_without_touching_any_of_it(): void
    {
        $installer = app(ProfileInstaller::class);
        $reconciler = app(Reconciler::class);

        $installer->install('personal', 'owner');

        $note = Note::create(['title' => 'Family recipe', 'body' => 'Secret sauce']);
        $page = WebsitePage::create(['title' => 'About', 'slug' => 'about', 'content' => 'This site predates the business.']);
        $task = Task::create(['title' => 'Water the plants']);

        $installer->install('small-business', 'owner');

        // Every module from both profiles is active.
        foreach (['website', 'notes', 'files', 'tasks', 'personal-databases', 'crm', 'inventory', 'invoicing'] as $slug) {
            $this->assertSame('active', InstalledModule::find($slug)?->status, "{$slug} must be active");
        }

        // Personal's content is byte-identical — same id, same
        // timestamps. If Small Business's install had re-provisioned
        // anything it touches in common, this would have changed.
        $this->assertSame($note->id, Note::first()->id);
        $this->assertSame('Family recipe', Note::first()->title);
        $this->assertEquals($note->created_at, Note::first()->created_at);

        $this->assertSame($page->id, WebsitePage::first()->id);
        $this->assertEquals($page->created_at, WebsitePage::first()->created_at);

        $this->assertSame($task->id, Task::first()->id);

        // Website is genuinely shared: two distinct referrers.
        $this->assertSame(2, $reconciler->referenceCount('website'));
        $this->assertEqualsCanonicalizing(
            ['personal', 'small-business'],
            ModuleReference::where('module_slug', 'website')->pluck('reference_identifier')->toArray(),
        );

        // Owner is the SAME role row both profiles grant to — extended,
        // not redefined. website.manage now has three distinct sources:
        // ModuleInstaller's own baseline grant (every module grants its
        // owner capability to whoever installs it, Phase 6b), plus each
        // profile independently granting it too.
        $this->assertSame(1, Role::where('role_slug', 'owner')->count());
        $this->assertEqualsCanonicalizing(
            ['module:website', 'personal', 'small-business'],
            RoleCapabilityGrant::where('role_slug', 'owner')->where('capability_slug', 'website.manage')
                ->pluck('granted_by_source')->toArray(),
        );

        // Staff and Customer are new roles Small Business created.
        $this->assertTrue($reconciler->roleHasCapability('staff', 'crm.manage'));
        $this->assertTrue($reconciler->roleHasCapability('customer', 'invoicing.view'));

        // invoicing really did pull in crm as a module_dependency —
        // not just because small-business's own manifest also lists
        // crm directly.
        $this->assertTrue(
            ModuleReference::where('module_slug', 'crm')
                ->where('reference_type', 'module_dependency')
                ->where('reference_identifier', 'invoicing')
                ->exists(),
        );
    }

    public function test_removing_small_business_leaves_personal_completely_intact(): void
    {
        $installer = app(ProfileInstaller::class);
        $reconciler = app(Reconciler::class);

        $installer->install('personal', 'owner');
        $note = Note::create(['title' => 'Family recipe']);
        $installer->install('small-business', 'owner');

        $reconciler->removeProfile('small-business');

        // Personal's own modules: completely unaffected.
        foreach (['website', 'notes', 'files', 'tasks', 'personal-databases'] as $slug) {
            $this->assertSame('active', InstalledModule::find($slug)?->status, "{$slug} must stay active");
        }
        $this->assertSame('Family recipe', Note::first()->title);

        // Small Business's own-only modules: orphaned, not deleted.
        // Critically, crm orphans too — even though nothing directly
        // removed its reference, because invoicing (the module that
        // was keeping it alive via a module_dependency reference) is
        // itself now orphaned. An orphaned module's own dependency
        // references must not keep propping up other modules forever.
        foreach (['crm', 'inventory', 'invoicing'] as $slug) {
            $this->assertSame('orphaned', InstalledModule::find($slug)?->status, "{$slug} must orphan");
        }
        $this->assertSame(0, $reconciler->referenceCount('crm'));
        $this->assertSame(0, $reconciler->referenceCount('invoicing'));

        // Website survives — Personal still references it — and its
        // reference count dropped from 2 to exactly 1.
        $this->assertSame('active', InstalledModule::find('website')?->status);
        $this->assertSame(1, $reconciler->referenceCount('website'));

        // Owner's website.manage grant from Personal (and from
        // ModuleInstaller's own baseline grant, which uninstall never
        // revokes — non-destructive removal, same rule as the module
        // row itself) survives even though Small Business's own grant
        // of the same capability to the same role was revoked (the N:1
        // capability rule, proven for real, not against a fake profile).
        $this->assertTrue($reconciler->roleHasCapability('owner', 'website.manage'));
        $this->assertEqualsCanonicalizing(
            ['module:website', 'personal'],
            RoleCapabilityGrant::where('role_slug', 'owner')->where('capability_slug', 'website.manage')
                ->pluck('granted_by_source')->toArray(),
        );

        // Owner's crm.manage grant from Small Business is gone — but
        // ModuleInstaller's own baseline grant for crm survives, same
        // non-destructive rule as the crm module row itself (orphaned,
        // not deleted). That grant alone doesn't reopen access: crm is
        // orphaned, so module.active still 404s it regardless of
        // capability, proven for real two lines down.
        $this->assertTrue($reconciler->roleHasCapability('owner', 'crm.manage'));
        $this->assertEqualsCanonicalizing(
            ['module:crm'],
            RoleCapabilityGrant::where('role_slug', 'owner')->where('capability_slug', 'crm.manage')
                ->pluck('granted_by_source')->toArray(),
        );

        // The whole app is still fully usable through its real API —
        // not just correct in the database.
        $this->getJson('/api/modules/notes/notes')->assertOk()->assertJsonFragment(['title' => 'Family recipe']);
        $this->getJson('/api/modules/crm/contacts')->assertNotFound();
    }

    public function test_small_business_can_be_reinstalled_later_and_reactivates_cleanly(): void
    {
        $installer = app(ProfileInstaller::class);
        $reconciler = app(Reconciler::class);

        $installer->install('personal', 'owner');
        $installer->install('small-business', 'owner');
        $reconciler->removeProfile('small-business');

        $this->assertSame('orphaned', InstalledModule::find('crm')?->status);

        $installer->install('small-business', 'owner');

        $this->assertSame('active', InstalledModule::find('crm')?->status);
        $this->assertSame('active', InstalledModule::find('invoicing')?->status);
        $this->getJson('/api/modules/crm/contacts')->assertOk();
    }
}
