<?php

namespace Moduloom\KnowledgeBase\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Moduloom\KnowledgeBase\Models\Article;

class ArticleController extends Controller
{
    public function index()
    {
        return Article::query()->latest()->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:kb_articles,slug', 'alpha_dash'],
            'category' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
        ]);

        return response()->json(Article::create($validated), 201);
    }

    public function destroy(Article $article)
    {
        $article->delete();

        return response()->noContent();
    }
}
