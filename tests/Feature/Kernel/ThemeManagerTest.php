<?php

namespace Tests\Feature\Kernel;

use App\Kernel\ProfileInstaller;
use App\Kernel\ThemeManager;
use App\Models\ThemeSelection;
use Moduloom\Website\Models\WebsitePage;
use RuntimeException;
use Tests\TestCase;

/**
 * Resolves the "Theme system" OPEN item from MASTER_ARCHITECTURE.md:
 * a theme governs only public-facing pages, is instance-scoped, is
 * adopted non-destructively on first install, and is explicitly
 * switchable afterward.
 */
class ThemeManagerTest extends TestCase
{
    public function test_a_fresh_instance_has_no_theme_selection_and_falls_back_to_default(): void
    {
        $this->assertSame(0, ThemeSelection::count());
        $this->assertSame('default', app(ThemeManager::class)->activeThemeSlug());
    }

    public function test_the_first_profile_installed_adopts_its_theme_as_the_instance_default(): void
    {
        app(ProfileInstaller::class)->install('personal', 'owner');

        $this->assertSame('personal-default', app(ThemeManager::class)->activeThemeSlug());
        $this->assertSame(1, ThemeSelection::count());
    }

    public function test_a_later_profile_never_overrides_an_already_chosen_theme(): void
    {
        $installer = app(ProfileInstaller::class);
        $themes = app(ThemeManager::class);

        $installer->install('personal', 'owner');
        $this->assertSame('personal-default', $themes->activeThemeSlug());

        $installer->install('small-business', 'owner');

        $this->assertSame(
            'personal-default',
            $themes->activeThemeSlug(),
            'installing a second profile must never silently change the look an earlier one already set',
        );
    }

    public function test_the_owner_can_explicitly_switch_to_a_real_theme(): void
    {
        $themes = app(ThemeManager::class);

        $themes->setActive('small-business-default', 'owner');

        $this->assertSame('small-business-default', $themes->activeThemeSlug());
        $this->assertSame(1, ThemeSelection::count());

        // Switching again appends a second row rather than updating
        // the first — the same append-only idiom as
        // profile_install_history.
        $themes->setActive('default', 'owner');
        $this->assertSame(2, ThemeSelection::count());
        $this->assertSame('default', $themes->activeThemeSlug());
    }

    public function test_switching_to_a_theme_with_no_real_layout_is_refused(): void
    {
        $this->expectException(RuntimeException::class);

        app(ThemeManager::class)->setActive('nonexistent-theme', 'owner');
    }

    public function test_wrapping_falls_back_to_default_when_the_active_slug_has_no_real_layout(): void
    {
        // personal-default has no real resources/views/themes/personal-default
        // directory — only 'default' and 'small-business-default' are
        // actually built. wrap() must degrade gracefully, not 500.
        app(ProfileInstaller::class)->install('personal', 'owner');

        $page = new WebsitePage(['title' => 'X', 'content' => 'Y']);
        $rendered = (string) app(ThemeManager::class)->wrap(view('website::page', ['page' => $page]));

        $this->assertStringContainsString('theme: default', $rendered);
    }

    public function test_wrapping_uses_the_explicitly_switched_theme_once_one_is_built(): void
    {
        $themes = app(ThemeManager::class);
        $themes->setActive('small-business-default', 'owner');

        $page = new WebsitePage(['title' => 'X', 'content' => 'Y']);
        $rendered = (string) $themes->wrap(view('website::page', ['page' => $page]));

        $this->assertStringContainsString('theme: small-business-default', $rendered);
    }

    public function test_available_themes_only_lists_themes_with_a_real_layout_on_disk(): void
    {
        $this->assertEqualsCanonicalizing(
            ['default', 'small-business-default'],
            app(ThemeManager::class)->availableThemes(),
        );
    }

    public function test_a_public_website_page_renders_wrapped_in_the_active_theme(): void
    {
        app(ProfileInstaller::class)->install('personal', 'owner');
        app(ThemeManager::class)->setActive('small-business-default', 'owner');

        WebsitePage::create([
            'title' => 'About',
            'slug' => 'about',
            'content' => 'Hello',
        ]);

        $this->get('/site/about')
            ->assertOk()
            ->assertSee('Hello')
            ->assertSee('Open for business'); // small-business-default's distinct header
    }
}
