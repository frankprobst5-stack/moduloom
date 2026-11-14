<?php

namespace Tests\Feature\Kernel;

use App\Kernel\ProfileInstaller;
use Tests\TestCase;

/**
 * The root URL must never show Laravel's generic scaffold page — a
 * fresh instance goes to the setup wizard, one that's already
 * installed something goes to its own dashboard.
 */
class RootRouteTest extends TestCase
{
    public function test_a_fresh_instance_redirects_to_setup(): void
    {
        $this->get('/')->assertRedirect('/setup');
    }

    public function test_an_instance_with_something_installed_redirects_to_the_dashboard(): void
    {
        app(ProfileInstaller::class)->install('personal', 'owner');

        $this->get('/')->assertRedirect('/dashboard');
    }
}
