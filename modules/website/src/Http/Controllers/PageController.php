<?php

namespace Moduloom\Website\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Moduloom\Website\Models\WebsitePage;

class PageController extends Controller
{
    public function index()
    {
        return WebsitePage::query()->latest()->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:website_pages,slug', 'alpha_dash'],
            'content' => ['nullable', 'string'],
        ]);

        return WebsitePage::create($validated);
    }

    public function update(Request $request, WebsitePage $page)
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
        ]);

        $page->update($validated);

        return $page;
    }

    public function destroy(WebsitePage $page)
    {
        $page->delete();

        return response()->noContent();
    }
}
