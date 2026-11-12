<?php

namespace Moduloom\Website\Http\Controllers;

use Illuminate\Routing\Controller;
use Moduloom\Website\Models\WebsitePage;

class PublicPageController extends Controller
{
    public function show(string $slug)
    {
        $page = WebsitePage::where('slug', $slug)->firstOrFail();

        return view('website::page', ['page' => $page]);
    }
}
