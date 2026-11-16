<?php

namespace Tests\Feature\Kernel;

use App\Kernel\ModuleInstaller;
use App\Models\User;
use Tests\TestCase;

/**
 * Phase 6b: being logged in (6a) proves an identity, not that it holds
 * a role with the capability a route needs. TestCase::setUp() already
 * authenticates an Owner by default — these tests are about what
 * happens to everyone else.
 */
class CapabilityEnforcementTest extends TestCase
{
    public function test_the_default_owner_can_use_a_module_it_installed(): void
    {
        app(ModuleInstaller::class)->install('notes', 'manual', 'owner');

        $this->postJson('/api/modules/notes/notes', ['title' => 'Owner can do this'])
            ->assertCreated();
    }

    public function test_a_logged_in_user_without_the_capability_is_forbidden(): void
    {
        app(ModuleInstaller::class)->install('notes', 'manual', 'owner');

        // A real account that was never assigned any role at all —
        // distinct from Tests\Feature\Auth\AuthTest's "not logged in"
        // scenarios. This is "logged in, but holds nothing."
        $this->actingAs(User::factory()->create());

        $this->postJson('/api/modules/notes/notes', ['title' => 'Should be refused'])
            ->assertForbidden();
    }

    public function test_the_default_owner_can_install_a_profile_and_a_standalone_module(): void
    {
        $this->postJson('/api/setup/install/personal')->assertOk();
        $this->postJson('/api/marketplace/install/forums')->assertNoContent();
        $this->postJson('/api/marketplace/uninstall/forums')->assertNoContent();
    }

    public function test_a_user_without_instance_manage_cannot_install_anything_instance_wide(): void
    {
        $this->actingAs(User::factory()->create());

        $this->postJson('/api/setup/install/personal')->assertForbidden();
        $this->postJson('/api/marketplace/install/notes')->assertForbidden();
    }
}
