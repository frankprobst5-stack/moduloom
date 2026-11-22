<?php

namespace Tests\Feature\Kernel;

use App\Kernel\ModuleInstaller;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * A module installed once, then shipping a new migration later, used
 * to have no way to pick it up on an instance that already installed
 * it — ModuleInstaller only ever ran migrations on first create. This
 * proves the real fix: install() runs migrate --path every time, and
 * Laravel's own migration repository (which file has already run, per
 * path) is what makes repeating that safe.
 */
class ModuleSchemaUpgradeTest extends TestCase
{
    private ?string $tempMigrationPath = null;

    protected function tearDown(): void
    {
        if ($this->tempMigrationPath && file_exists($this->tempMigrationPath)) {
            unlink($this->tempMigrationPath);
        }

        parent::tearDown();
    }

    public function test_a_migration_shipped_after_first_install_runs_on_the_next_install_call(): void
    {
        app(ModuleInstaller::class)->install('notes', 'manual', 'owner');

        $this->assertTrue(Schema::hasTable('notes'));
        $this->assertFalse(Schema::hasColumn('notes', 'pinned'), 'sanity check: the new column must not exist yet');

        // Simulates a new version of the notes module shipping a
        // migration after this instance already installed it.
        $this->tempMigrationPath = base_path('modules/notes/database/migrations/2099_01_01_000000_add_pinned_to_notes_table.php');
        file_put_contents($this->tempMigrationPath, <<<'PHP'
            <?php

            use Illuminate\Database\Migrations\Migration;
            use Illuminate\Database\Schema\Blueprint;
            use Illuminate\Support\Facades\Schema;

            return new class extends Migration
            {
                public function up(): void
                {
                    Schema::table('notes', function (Blueprint $table) {
                        $table->boolean('pinned')->default(false);
                    });
                }

                public function down(): void
                {
                    Schema::table('notes', function (Blueprint $table) {
                        $table->dropColumn('pinned');
                    });
                }
            };
            PHP
        );

        // A second referrer, not a fresh install — this is the whole
        // point: an *already-installed* module still picks up new
        // schema the next time anything calls install() on it.
        app(ModuleInstaller::class)->install('notes', 'manual', 'a-second-referrer');

        $this->assertTrue(Schema::hasColumn('notes', 'pinned'));
    }
}
