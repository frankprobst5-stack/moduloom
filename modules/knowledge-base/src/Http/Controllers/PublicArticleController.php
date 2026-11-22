<?php

namespace Moduloom\KnowledgeBase\Http\Controllers;

use App\Kernel\FederationFeed;
use App\Kernel\ThemeManager;
use Illuminate\Routing\Controller;
use Moduloom\KnowledgeBase\Models\Article;

class PublicArticleController extends Controller
{
    public function show(string $slug, ThemeManager $themes)
    {
        $article = Article::where('slug', $slug)->firstOrFail();

        return $themes->wrap(view('knowledge-base::article', ['article' => $article]));
    }

    public function feed()
    {
        $items = Article::query()->get()->map(fn (Article $article) => [
            'title' => $article->title,
            'slug' => $article->slug,
            'content' => $article->content,
            'published_at' => $article->updated_at?->toIso8601String(),
            'canonical_url' => url("/kb/{$article->slug}"),
        ]);

        return FederationFeed::respond('knowledge-base', $items);
    }
}
