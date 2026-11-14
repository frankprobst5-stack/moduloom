<?php

namespace App\Http\Controllers;

use App\Kernel\ModuleInstaller;
use App\Kernel\Reconciler;
use App\Models\InstalledModule;
use Illuminate\Routing\Controller;

/**
 * "Module marketplace UI (browse/install beyond your current
 * profile)" — MASTER_ROADMAP.md, Phase 5. Every registered module,
 * not just the ones a profile happened to bundle, with real installed
 * state and reference counts so the UI can be honest about what a
 * removal would actually do.
 */
class ModuleMarketplaceController extends Controller
{
    public function index(ModuleInstaller $installer, Reconciler $reconciler)
    {
        $installed = InstalledModule::whereIn('status', ['active', 'disabled'])
            ->pluck('module_slug')
            ->all();

        return collect(config('modules', []))
            ->map(function (string $class, string $slug) use ($installer, $reconciler, $installed) {
                $manifest = $installer->manifestFor($slug);

                return [
                    'slug' => $manifest->slug(),
                    'label' => $manifest->navEntries()[0]['label'] ?? $manifest->slug(),
                    'description' => $manifest->description(),
                    'category' => $manifest->category(),
                    'requires' => $manifest->requires(),
                    'installed' => in_array($slug, $installed, true),
                    'reference_count' => $reconciler->referenceCount($slug),
                ];
            })
            ->sortBy([['category', 'asc'], ['label', 'asc']])
            ->values();
    }

    public function install(string $slug, ModuleInstaller $installer)
    {
        $installer->install($slug, 'manual', 'marketplace');

        return response()->noContent();
    }

    public function uninstall(string $slug, ModuleInstaller $installer)
    {
        $installer->uninstall($slug, 'manual', 'marketplace');

        return response()->noContent();
    }
}
