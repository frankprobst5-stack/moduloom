<?php

namespace Moduloom\Forums\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Moduloom\Forums\Models\Thread;

class ThreadController extends Controller
{
    public function index()
    {
        return Thread::query()->withCount('replies')->latest()->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author_name' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        return response()->json(Thread::create($validated), 201);
    }

    public function show(Thread $thread)
    {
        return $thread->load('replies');
    }

    public function destroy(Thread $thread)
    {
        $thread->delete();

        return response()->noContent();
    }
}
