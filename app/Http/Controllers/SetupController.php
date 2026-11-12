<?php

namespace App\Http\Controllers;

use App\Kernel\ProfileInstaller;
use App\Kernel\ProfileManifest;
use App\Models\ProfileInstallHistory;
use Illuminate\Routing\Controller;

class SetupController extends Controller
{
    public function profiles()
    {
        $registry = config('profiles', []);

        return collect($registry)->map(function (string $class, string $slug) {
            /** @var ProfileManifest $manifest */
            $manifest = app($class);

            return [
                'slug' => $manifest->slug(),
                'label' => $manifest->label(),
                'modules' => $manifest->modules(),
                'theme_slug' => $manifest->themeSlug(),
                'already_installed' => ProfileInstallHistory::where('profile_slug', $slug)->exists(),
            ];
        })->values();
    }

    public function install(string $slug, ProfileInstaller $installer)
    {
        $manifest = $installer->install($slug, 'setup-wizard');

        return [
            'slug' => $manifest->slug(),
            'label' => $manifest->label(),
            'modules' => $manifest->modules(),
            'theme_slug' => $manifest->themeSlug(),
        ];
    }
}
