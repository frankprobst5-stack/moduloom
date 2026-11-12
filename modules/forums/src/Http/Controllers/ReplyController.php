<?php

namespace Moduloom\Forums\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Moduloom\Forums\Models\Thread;

class ReplyController extends Controller
{
    public function store(Request $request, Thread $thread)
    {
        $validated = $request->validate([
            'author_name' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        return response()->json($thread->replies()->create($validated), 201);
    }
}
