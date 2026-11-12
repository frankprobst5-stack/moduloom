<?php

namespace App\Http\Controllers;

use App\Kernel\ModuleInstaller;
use App\Models\InstalledModule;
use App\Models\ProfileInstallHistory;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function nav(ModuleInstaller $installer)
    {
        return InstalledModule::whereIn('status', ['active', 'disabled'])
            ->get()
            ->flatMap(fn (InstalledModule $module) => $installer->manifestFor($module->module_slug)->navEntries())
            ->sortBy('sort_weight')
            ->values();
    }

    public function latestInstall()
    {
        return ProfileInstallHistory::latest('installed_at')->first();
    }
}
