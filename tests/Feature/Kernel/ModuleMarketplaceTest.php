<?php

namespace Tests\Feature\Kernel;

use App\Kernel\ProfileInstaller;
use App\Kernel\Reconciler;
use Moduloom\Forums\Models\Thread;
use Tests\TestCase;

/**
 * "Module marketplace UI (browse/install beyond your current
 * profile)" — MASTER_ROADMAP.md, Phase 5.
 */
class ModuleMarketplaceTest extends TestCase
{
    public function test_it_lists_every_registered_module_with_install_state(): void
    {
        $modules = $this->getJson('/api/marketplace/modules')->assertOk()->json();

        $this->assertCount(count(config('modules')), $modules);

        $notes = collect($modules)->firstWhere('slug', 'notes');
        $this->assertSame('Productivity', $notes['category']);
        $this->assertFalse($notes['installed']);
        $this->assertSame(0, $notes['reference_count']);
    }

    public function test_installing_a_standalone_module_through_the_marketplace_works(): void
    {
        $this->postJson('/api/marketplace/install/forums')->assertNoContent();

        $modules = $this->getJson('/api/marketplace/modules')->json();
        $forums = collect($modules)->firstWhere('slug', 'forums');

        $this->assertTrue($forums['installed']);
        $this->assertSame(1, $forums['reference_count']);

        $this->postJson('/api/modules/forums/threads', ['title' => 'Hi', 'author_name' => 'A', 'body' => 'B'])
            ->assertCreated();
    }

    public function test_uninstalling_a_standalone_module_orphans_it_but_keeps_its_data(): void
    {
        $this->postJson('/api/marketplace/install/forums')->assertNoContent();
        $this->postJson('/api/modules/forums/threads', ['title' => 'Hi', 'author_name' => 'A', 'body' => 'B'])
            ->assertCreated();

        $this->postJson('/api/marketplace/uninstall/forums')->assertNoContent();

        $modules = $this->getJson('/api/marketplace/modules')->json();
        $forums = collect($modules)->firstWhere('slug', 'forums');
        $this->assertFalse($forums['installed']);

        $this->getJson('/api/modules/forums/threads')->assertNotFound();
        $this->assertSame(1, Thread::count());
    }

    /**
     * Installing a module standalone through the marketplace must not
     * disturb anything a profile already installed — the same
     * additive-overlay rule Phase 3 proved for profiles applies here
     * too, since both paths go through the same ModuleInstaller.
     */
    public function test_marketplace_installing_a_module_a_profile_already_bundled_does_not_duplicate_it(): void
    {
        app(ProfileInstaller::class)->install('small-business', 'owner');

        $this->postJson('/api/marketplace/install/website')->assertNoContent();

        $this->assertSame(2, app(Reconciler::class)->referenceCount('website'));

        $modules = $this->getJson('/api/marketplace/modules')->json();
        $website = collect($modules)->firstWhere('slug', 'website');
        $this->assertSame(2, $website['reference_count']);
    }
}
