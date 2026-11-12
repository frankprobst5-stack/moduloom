<?php

namespace Moduloom\Notes\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Moduloom\Notes\Models\Note;

class NoteController extends Controller
{
    public function index()
    {
        return Note::query()->latest()->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
        ]);

        return Note::create($validated);
    }

    public function update(Request $request, Note $note)
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
        ]);

        $note->update($validated);

        return $note;
    }

    public function destroy(Note $note)
    {
        $note->delete();

        return response()->noContent();
    }
}
