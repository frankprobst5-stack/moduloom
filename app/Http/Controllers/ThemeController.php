<?php

namespace App\Http\Controllers;

use App\Kernel\ThemeManager;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use RuntimeException;

class ThemeController extends Controller
{
    public function show(ThemeManager $themes)
    {
        return [
            'active' => $themes->activeThemeSlug(),
            'available' => $themes->availableThemes(),
        ];
    }

    public function activate(Request $request, ThemeManager $themes)
    {
        $validated = $request->validate(['theme_slug' => ['required', 'string']]);

        try {
            $themes->setActive($validated['theme_slug'], 'dashboard');
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return $this->show($themes);
    }
}
