<?php

namespace App\Http\Controllers;

use App\Kernel\ModuleInstaller;
use App\Kernel\Reconciler;
use App\Models\InstalledModule;
use Illuminate\Routing\Controller;
use RuntimeException;

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
        $statuses = InstalledModule::whereIn('status', ['active', 'disabled', 'orphaned'])
            ->pluck('status', 'module_slug');

        return collect(config('modules', []))
            ->map(function (string $class, string $slug) use ($installer, $reconciler, $statuses) {
                $manifest = $installer->manifestFor($slug);
                $status = $statuses->get($slug);

                return [
                    'slug' => $manifest->slug(),
                    'label' => $manifest->navEntries()[0]['label'] ?? $manifest->slug(),
                    'description' => $manifest->description(),
                    'category' => $manifest->category(),
                    'requires' => $manifest->requires(),
                    'installed' => in_array($status, ['active', 'disabled'], true),
                    'orphaned' => $status === 'orphaned',
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

    public function purge(string $slug, ModuleInstaller $installer)
    {
        try {
            $installer->purge($slug);
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->noContent();
    }
}
