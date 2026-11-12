<?php

namespace Moduloom\PublicInformation\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Moduloom\PublicInformation\Models\Notice;

class NoticeController extends Controller
{
    public function index()
    {
        return Notice::query()->latest('published_at')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
        ]);

        return response()->json(Notice::create($validated), 201);
    }

    public function destroy(Notice $notice)
    {
        $notice->delete();

        return response()->noContent();
    }
}
