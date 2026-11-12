<?php

namespace Moduloom\KnowledgeBase\Http\Controllers;

use Illuminate\Routing\Controller;
use Moduloom\KnowledgeBase\Models\Article;

class PublicArticleController extends Controller
{
    public function show(string $slug)
    {
        $article = Article::where('slug', $slug)->firstOrFail();

        return view('knowledge-base::article', ['article' => $article]);
    }
}
