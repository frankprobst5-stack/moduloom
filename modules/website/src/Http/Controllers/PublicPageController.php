<?php

namespace Moduloom\Website\Http\Controllers;

use App\Kernel\ThemeManager;
use Illuminate\Routing\Controller;
use Moduloom\Website\Models\WebsitePage;

class PublicPageController extends Controller
{
    public function show(string $slug, ThemeManager $themes)
    {
        $page = WebsitePage::where('slug', $slug)->firstOrFail();

        return $themes->wrap(view('website::page', ['page' => $page]));
    }
}
