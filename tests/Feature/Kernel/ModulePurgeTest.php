<?php

namespace Tests\Feature\Kernel;

use App\Kernel\ModuleInstaller;
use App\Kernel\Reconciler;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * MASTER_ARCHITECTURE.md's non-destructive removal rule always said
 * actually deleting a module's data would have to be "a separate,
 * explicitly-confirmed, distinct action" — this is that action, and it
 * only existed as a documented intention until now.
 */
class ModulePurgeTest extends TestCase
{
    public function test_purging_an_orphaned_module_drops_its_schema_and_kernel_record(): void
    {
        app(ModuleInstaller::class)->install('notes', 'manual', 'owner');
        $this->assertTrue(Schema::hasTable('notes'));
        $this->assertTrue(app(Reconciler::class)->roleHasCapability('owner', 'notes.manage'));

        app(ModuleInstaller::class)->uninstall('notes', 'manual', 'owner');
        $this->assertDatabaseHas('installed_modules', ['module_slug' => 'notes', 'status' => 'orphaned']);

        $this->deleteJson('/api/marketplace/purge/notes')->assertNoContent();

        $this->assertFalse(Schema::hasTable('notes'));
        $this->assertDatabaseMissing('installed_modules', ['module_slug' => 'notes']);
        $this->assertFalse(app(Reconciler::class)->roleHasCapability('owner', 'notes.manage'));

        // Confirmed gone from the marketplace's own view too, not just
        // the raw tables — back to looking like it was never installed.
        $listing = $this->getJson('/api/marketplace/modules')->json();
        $notes = collect($listing)->firstWhere('slug', 'notes');
        $this->assertFalse($notes['installed']);
        $this->assertFalse($notes['orphaned']);
    }

    public function test_an_active_module_cannot_be_purged(): void
    {
        app(ModuleInstaller::class)->install('notes', 'manual', 'owner');

        $this->deleteJson('/api/marketplace/purge/notes')->assertUnprocessable();
        $this->assertDatabaseHas('installed_modules', ['module_slug' => 'notes', 'status' => 'active']);
    }

    public function test_a_module_that_was_never_installed_cannot_be_purged(): void
    {
        $this->deleteJson('/api/marketplace/purge/notes')->assertUnprocessable();
    }

    public function test_a_user_without_instance_manage_cannot_purge_anything(): void
    {
        app(ModuleInstaller::class)->install('notes', 'manual', 'owner');
        app(ModuleInstaller::class)->uninstall('notes', 'manual', 'owner');

        $this->actingAs(User::factory()->create());

        $this->deleteJson('/api/marketplace/purge/notes')->assertForbidden();
    }
}
