<?php

namespace Moduloom\Website\Http\Controllers;

use App\Kernel\FederationFeed;
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

    public function feed()
    {
        $items = WebsitePage::query()->get()->map(fn (WebsitePage $page) => [
            'title' => $page->title,
            'slug' => $page->slug,
            'content' => $page->content,
            'published_at' => $page->updated_at?->toIso8601String(),
            'canonical_url' => url("/site/{$page->slug}"),
        ]);

        return FederationFeed::respond('website', $items);
    }
}
