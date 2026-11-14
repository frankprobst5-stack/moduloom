<?php

namespace App\Kernel;

use App\Models\ThemeSelection;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View as ViewFacade;
use RuntimeException;

/**
 * Resolves and applies the instance's active theme. A theme governs
 * ONLY public-facing pages (Website, Knowledge base, Public
 * information) — never the admin shell. See MASTER_ARCHITECTURE.md's
 * "Theme system" section for the reasoning: once ecosystems evolve
 * together there's no single "current ecosystem" left to theme a
 * shared dashboard by, and every admin tool this platform takes
 * inspiration from (Directus, Strapi, NocoDB, Appsmith) keeps one
 * fixed admin chrome regardless of theme for exactly that reason.
 *
 * A theme is a directory under resources/views/themes/{slug}/ with at
 * least a layout.blade.php. Themes are presentation-only — no
 * migrations, no capabilities, not modules, not profiles.
 */
class ThemeManager
{
    private const FALLBACK = 'default';

    public function activeThemeSlug(): string
    {
        return ThemeSelection::query()->latest('id')->value('theme_slug') ?? self::FALLBACK;
    }

    /**
     * Called by ProfileInstaller after a profile's modules are
     * installed. Only adopts the profile's theme if NO theme has ever
     * been chosen on this instance — a later profile must never
     * silently change a look the owner (or an earlier profile) already
     * set. That's the same non-destructive rule invariant 2 applies to
     * module configuration, applied here to presentation.
     */
    public function adoptDefaultIfUnset(string $themeSlug, string $setBy): void
    {
        if (ThemeSelection::query()->exists()) {
            return;
        }

        ThemeSelection::create(['theme_slug' => $themeSlug, 'set_by' => $setBy]);
    }

    /**
     * Explicitly switches the active theme. Refuses a slug with no
     * real layout on disk rather than silently falling back — an
     * explicit switch should fail loudly if it can't do what was
     * asked; activeThemeSlug()'s fallback is only for themes a profile
     * named but nobody has designed yet.
     */
    public function setActive(string $themeSlug, string $setBy): void
    {
        if (! $this->themeExists($themeSlug)) {
            throw new RuntimeException("No theme named [{$themeSlug}] exists on disk.");
        }

        ThemeSelection::create(['theme_slug' => $themeSlug, 'set_by' => $setBy]);
    }

    /**
     * Wraps a module's public-facing content view in the active
     * theme's layout. Falls back to the built-in default theme if the
     * active slug (which may just be a profile's themeSlug() that
     * nobody has designed a real layout for yet) has no layout on
     * disk — every profile ships a themeSlug(), not every theme has
     * been built, and that gap should degrade gracefully, not 500.
     */
    public function wrap(View $content): View
    {
        $slug = $this->activeThemeSlug();

        if (! $this->themeExists($slug)) {
            $slug = self::FALLBACK;
        }

        return ViewFacade::make("themes.{$slug}.layout", ['content' => $content]);
    }

    /**
     * @return string[] slugs of themes that actually have a layout on
     *                  disk, as opposed to ones only named by a
     *                  profile's themeSlug().
     */
    public function availableThemes(): array
    {
        $themesPath = resource_path('views/themes');

        if (! File::isDirectory($themesPath)) {
            return [];
        }

        return collect(File::directories($themesPath))
            ->map(fn (string $path) => basename($path))
            ->filter(fn (string $slug) => $this->themeExists($slug))
            ->values()
            ->all();
    }

    private function themeExists(string $slug): bool
    {
        return ViewFacade::exists("themes.{$slug}.layout");
    }
}
