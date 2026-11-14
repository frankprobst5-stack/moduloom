<?php

namespace Tests\Feature\Profiles;

use App\Kernel\ProfileInstaller;
use App\Kernel\Reconciler;
use App\Models\InstalledModule;
use App\Models\ModuleReference;
use App\Models\Role;
use App\Models\RoleCapabilityGrant;
use Tests\TestCase;

/**
 * Phase 4's acceptance scope, from MASTER_ROADMAP.md: the remaining
 * ecosystems (Community, Education, Enterprise, Government) install
 * correctly and their modules are real and usable — mostly
 * module-writing now that Phases 0-3 proved the kernel and profile
 * mechanisms under real load.
 */
class RemainingEcosystemsTest extends TestCase
{
    public function test_community_installs_all_four_of_its_modules(): void
    {
        app(ProfileInstaller::class)->install('community', 'owner');

        foreach (['forums', 'membership', 'events', 'directories'] as $slug) {
            $this->assertSame('active', InstalledModule::find($slug)?->status, "{$slug} must be active");
        }
    }

    public function test_education_installs_its_modules_and_reuses_website(): void
    {
        $installer = app(ProfileInstaller::class);
        $reconciler = app(Reconciler::class);

        $installer->install('education', 'owner');

        foreach (['website', 'classrooms', 'assignments', 'knowledge-base'] as $slug) {
            $this->assertSame('active', InstalledModule::find($slug)?->status, "{$slug} must be active");
        }

        // assignments really does require classrooms — a second,
        // distinct referrer beyond education's own direct profile
        // reference to classrooms.
        $this->assertTrue(
            ModuleReference::where('module_slug', 'classrooms')
                ->where('reference_type', 'module_dependency')
                ->where('reference_identifier', 'assignments')
                ->exists(),
        );
        $this->assertSame(2, $reconciler->referenceCount('classrooms'));
    }

    public function test_enterprise_installs_all_four_of_its_modules(): void
    {
        app(ProfileInstaller::class)->install('enterprise', 'owner');

        foreach (['documents', 'workflows', 'projects', 'applications'] as $slug) {
            $this->assertSame('active', InstalledModule::find($slug)?->status, "{$slug} must be active");
        }
    }

    public function test_government_installs_its_modules_and_reuses_workflows(): void
    {
        app(ProfileInstaller::class)->install('government', 'owner');

        foreach (['public-information', 'forms', 'records', 'workflows'] as $slug) {
            $this->assertSame('active', InstalledModule::find($slug)?->status, "{$slug} must be active");
        }
    }

    public function test_enterprise_and_government_genuinely_share_the_workflows_module(): void
    {
        $reconciler = app(Reconciler::class);
        $installer = app(ProfileInstaller::class);

        $installer->install('enterprise', 'owner');
        $installer->install('government', 'owner');

        $this->assertSame(2, $reconciler->referenceCount('workflows'));
        $this->assertEqualsCanonicalizing(
            ['enterprise', 'government'],
            RoleCapabilityGrant::where('role_slug', 'customer')->where('capability_slug', 'workflows.view')
                ->pluck('granted_by_source')->toArray(),
        );
        $this->assertSame(1, Role::where('role_slug', 'customer')->count());
    }

    /**
     * This is the exact scenario MASTER_ARCHITECTURE.md's docblock for
     * invariant 3 names directly, now proven for real: Community
     * attaches forums.view onto the Customer role Small Business
     * already created, rather than creating a second Customer role.
     */
    public function test_community_extends_the_customer_role_small_business_already_created(): void
    {
        $installer = app(ProfileInstaller::class);

        $installer->install('personal', 'owner');
        $installer->install('small-business', 'owner');

        $this->assertSame(1, Role::where('role_slug', 'customer')->count());
        $this->assertFalse(app(Reconciler::class)->roleHasCapability('customer', 'forums.view'));

        $installer->install('community', 'owner');

        $this->assertSame(
            1,
            Role::where('role_slug', 'customer')->count(),
            'Community must extend the existing Customer role, never create a second one',
        );
        $this->assertEqualsCanonicalizing(
            ['community'],
            RoleCapabilityGrant::where('role_slug', 'customer')->where('capability_slug', 'forums.view')
                ->pluck('granted_by_source')->toArray(),
        );
        // Small Business's own Customer grant (invoicing.view) is
        // untouched — Community only added to the role, it didn't
        // reset it.
        $this->assertTrue(app(Reconciler::class)->roleHasCapability('customer', 'invoicing.view'));
    }

    public function test_forums_and_classrooms_chains_are_usable_through_their_real_apis(): void
    {
        app(ProfileInstaller::class)->install('community', 'owner');
        app(ProfileInstaller::class)->install('education', 'owner');

        $thread = $this->postJson('/api/modules/forums/threads', [
            'title' => 'Welcome',
            'author_name' => 'Dana',
            'body' => 'Hello everyone',
        ])->assertCreated()->json();

        $this->postJson("/api/modules/forums/threads/{$thread['id']}/replies", [
            'author_name' => 'Sam',
            'body' => 'Hi Dana!',
        ])->assertCreated();

        $this->getJson("/api/modules/forums/threads/{$thread['id']}")
            ->assertOk()
            ->assertJsonFragment(['body' => 'Hi Dana!']);

        $classroom = $this->postJson('/api/modules/classrooms/classrooms', ['name' => 'Room 101'])
            ->assertCreated()->json();

        $assignment = $this->postJson('/api/modules/assignments/assignments', [
            'classroom_id' => $classroom['id'],
            'title' => 'Essay 1',
        ])->assertCreated()->json();

        $this->postJson("/api/modules/assignments/assignments/{$assignment['id']}/submissions", [
            'student_name' => 'Jordan',
            'content' => 'My essay',
        ])->assertCreated();

        $this->getJson('/api/modules/assignments/assignments')
            ->assertOk()
            ->assertJsonFragment(['submissions_count' => 1]);
    }
}
