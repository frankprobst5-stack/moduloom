<?php

namespace Tests\Feature\Kernel;

use App\Kernel\ProfileInstaller;
use App\Models\User;
use Tests\TestCase;

/**
 * Owner-only account creation: set the password directly, no invite
 * link, since this app has no real outbound mail (MAIL_MAILER=log).
 */
class TeamTest extends TestCase
{
    public function test_the_owner_can_list_and_create_team_accounts(): void
    {
        // Small Business creates the 'staff' role — without a role to
        // assign, there'd be nothing meaningful to create yet.
        app(ProfileInstaller::class)->install('small-business', 'owner');

        $this->getJson('/api/team')->assertOk()->assertJsonFragment(['role_slug' => 'staff']);

        $this->postJson('/api/team', [
            'name' => 'Staff Member',
            'email' => 'staff@example.com',
            'password' => 'correct-horse',
            'role_slug' => 'staff',
        ])->assertCreated()->assertJsonFragment(['email' => 'staff@example.com']);

        $this->assertDatabaseHas('users', ['email' => 'staff@example.com']);

        $staff = User::where('email', 'staff@example.com')->first();
        $this->assertTrue($staff->hasCapability('crm.manage'));
    }

    public function test_creating_an_account_with_a_role_that_does_not_exist_is_rejected(): void
    {
        $this->postJson('/api/team', [
            'name' => 'Nobody',
            'email' => 'nobody@example.com',
            'password' => 'correct-horse',
            'role_slug' => 'super-admin',
        ])->assertUnprocessable();

        $this->assertDatabaseMissing('users', ['email' => 'nobody@example.com']);
    }

    public function test_a_user_without_instance_manage_cannot_see_or_create_team_accounts(): void
    {
        $this->actingAs(User::factory()->create());

        $this->getJson('/api/team')->assertForbidden();
        $this->postJson('/api/team', [
            'name' => 'Intruder',
            'email' => 'intruder@example.com',
            'password' => 'correct-horse',
            'role_slug' => 'owner',
        ])->assertForbidden();
    }
}
