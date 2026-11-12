<?php

namespace Moduloom\Directories\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Moduloom\Directories\Models\DirectoryEntry;

class DirectoryEntryController extends Controller
{
    public function index()
    {
        return DirectoryEntry::query()->orderBy('name')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'contact_info' => ['nullable', 'string', 'max:255'],
        ]);

        return response()->json(DirectoryEntry::create($validated), 201);
    }

    public function destroy(DirectoryEntry $directoryEntry)
    {
        $directoryEntry->delete();

        return response()->noContent();
    }
}
