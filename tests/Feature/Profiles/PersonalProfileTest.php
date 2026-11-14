<?php

namespace Tests\Feature\Profiles;

use App\Kernel\ProfileInstaller;
use App\Models\InstalledModule;
use App\Models\ProfileInstallHistory;
use App\Models\Role;
use App\Models\RoleCapabilityGrant;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Phase 2's acceptance criterion, from MASTER_ROADMAP.md: a fresh
 * instance can install the Personal profile end-to-end on a clean
 * database and land on a working onboarding wizard.
 */
class PersonalProfileTest extends TestCase
{
    public function test_installing_personal_provisions_every_module_and_the_owner_role(): void
    {
        app(ProfileInstaller::class)->install('personal', 'test');

        foreach (['website', 'notes', 'tasks', 'files', 'personal-databases'] as $slug) {
            $this->assertSame('active', InstalledModule::find($slug)?->status, "{$slug} must be active");
        }

        $this->assertSame(1, Role::count(), 'only the Owner role should exist');
        $this->assertSame(5, RoleCapabilityGrant::where('granted_by_source', 'personal')->count());
        $this->assertSame(1, ProfileInstallHistory::where('profile_slug', 'personal')->count());
    }

    public function test_the_setup_wizard_can_list_and_install_the_profile_over_http(): void
    {
        $this->getJson('/api/setup/profiles')
            ->assertOk()
            ->assertJsonFragment(['slug' => 'personal', 'already_installed' => false]);

        $this->postJson('/api/setup/install/personal')->assertOk();

        $this->getJson('/api/setup/profiles')
            ->assertJsonFragment(['slug' => 'personal', 'already_installed' => true]);
    }

    public function test_the_dashboard_nav_reflects_every_installed_module_in_sort_order(): void
    {
        app(ProfileInstaller::class)->install('personal', 'test');

        $this->getJson('/api/dashboard/nav')
            ->assertOk()
            ->assertJsonPath('0.slug', 'website')
            ->assertJsonPath('1.slug', 'notes')
            ->assertJsonPath('2.slug', 'tasks')
            ->assertJsonPath('3.slug', 'files')
            ->assertJsonPath('4.slug', 'personal-databases');
    }

    public function test_the_onboarding_screen_can_read_back_what_was_just_installed(): void
    {
        app(ProfileInstaller::class)->install('personal', 'setup-wizard');

        $this->getJson('/api/dashboard/latest-install')
            ->assertOk()
            ->assertJsonPath('profile_slug', 'personal')
            ->assertJsonPath('version', '1.0.0');
    }

    public function test_every_module_is_actually_usable_once_personal_is_installed(): void
    {
        Storage::fake('public');
        app(ProfileInstaller::class)->install('personal', 'test');

        $this->postJson('/api/modules/website/pages', ['title' => 'About', 'slug' => 'about', 'content' => 'Hello'])
            ->assertCreated();
        $this->get('/site/about')->assertOk()->assertSee('Hello');

        $this->postJson('/api/modules/notes/notes', ['title' => 'Groceries'])->assertCreated();
        $this->getJson('/api/modules/notes/notes')->assertJsonFragment(['title' => 'Groceries']);

        $this->postJson('/api/modules/tasks/tasks', ['title' => 'Call the bank'])->assertCreated();
        $this->getJson('/api/modules/tasks/tasks')->assertJsonFragment(['title' => 'Call the bank']);

        $this->post('/api/modules/files/files', [
            'file' => UploadedFile::fake()->create('receipt.pdf', 10),
        ])->assertCreated();
        $this->getJson('/api/modules/files/files')->assertJsonFragment(['original_name' => 'receipt.pdf']);

        $collection = $this->postJson('/api/modules/personal-databases/collections', ['name' => 'Recipes'])
            ->assertCreated()->json();
        $this->postJson("/api/modules/personal-databases/collections/{$collection['id']}/records", [
            'data' => ['dish' => 'lasagna'],
        ])->assertCreated();
        $this->getJson("/api/modules/personal-databases/collections/{$collection['id']}/records")
            ->assertJsonFragment(['dish' => 'lasagna']);
    }
}
