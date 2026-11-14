<?php

namespace Moduloom\KnowledgeBase\Http\Controllers;

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
}
