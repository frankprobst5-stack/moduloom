<?php

namespace Tests\Feature\Modules;

use App\Kernel\ModuleInstaller;
use App\Kernel\Reconciler;
use App\Models\InstalledModule;
use Illuminate\Support\Facades\Schema;
use Moduloom\Notes\Models\Note;
use Tests\Support\FakeDependentManifest;
use Tests\TestCase;

/**
 * Phase 1's acceptance criterion, from MASTER_ROADMAP.md: Notes can be
 * installed, used, and manually uninstalled without touching any other
 * part of the system, and uninstall correctly refuses [to actually
 * remove the module] if a second module later declares a dependency
 * on it.
 */
class NotesModuleTest extends TestCase
{
    private ModuleInstaller $installer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->installer = app(ModuleInstaller::class);
    }

    public function test_installing_notes_creates_its_table_and_kernel_row(): void
    {
        $this->assertFalse(Schema::hasTable('notes'), 'sanity check: table must not exist before install');

        $this->installer->install('notes', 'manual', 'owner');

        $this->assertTrue(Schema::hasTable('notes'));
        $this->assertSame('active', InstalledModule::find('notes')->status);
    }

    public function test_installed_notes_is_fully_usable_through_its_real_api(): void
    {
        $this->installer->install('notes', 'manual', 'owner');

        $created = $this->postJson('/api/modules/notes/notes', [
            'title' => 'Buy groceries',
            'body' => 'Milk, eggs, bread',
        ])->assertCreated()->json();

        $this->getJson('/api/modules/notes/notes')
            ->assertOk()
            ->assertJsonFragment(['title' => 'Buy groceries']);

        $this->deleteJson("/api/modules/notes/notes/{$created['id']}")
            ->assertNoContent();

        $this->assertDatabaseCount('notes', 0);
    }

    public function test_uninstalling_the_only_referrer_orphans_it_and_blocks_its_api(): void
    {
        $this->installer->install('notes', 'manual', 'owner');

        $this->postJson('/api/modules/notes/notes', ['title' => 'Survives the uninstall'])
            ->assertCreated();

        $this->installer->uninstall('notes', 'manual', 'owner');

        $this->assertSame('orphaned', InstalledModule::find('notes')->status);

        // The module is gone from the outside...
        $this->getJson('/api/modules/notes/notes')->assertNotFound();

        // ...but its data was never touched. Removal only ever orphans.
        $this->assertTrue(Schema::hasTable('notes'));
        $this->assertSame(1, Note::count());
        $this->assertSame('Survives the uninstall', Note::first()->title);
    }

    public function test_uninstalling_does_not_disturb_notes_while_a_dependent_module_still_needs_it(): void
    {
        config(['modules.fake-dependent' => FakeDependentManifest::class]);

        $this->installer->install('notes', 'manual', 'owner');
        $this->installer->install('fake-dependent', 'manual', 'owner');

        // fake-dependent's own install pulled in notes as a
        // module_dependency referrer, in addition to the manual one
        // already there.
        $this->assertSame(2, app(Reconciler::class)->referenceCount('notes'));

        // Removing the manual referrer must leave notes installed —
        // fake-dependent still references it.
        $this->installer->uninstall('notes', 'manual', 'owner');

        $this->assertSame('active', InstalledModule::find('notes')->status);
        $this->getJson('/api/modules/notes/notes')->assertOk();

        // Only once fake-dependent is also removed does notes orphan.
        $this->installer->uninstall('notes', 'module_dependency', 'fake-dependent');

        $this->assertSame('orphaned', InstalledModule::find('notes')->status);
    }
}
