<?php

namespace Moduloom\Records\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Moduloom\Records\Models\PublicRecord;

class PublicRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = PublicRecord::query()->latest('filed_at');

        if ($search = $request->query('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        return $query->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'record_type' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'is_redacted' => ['sometimes', 'boolean'],
            'filed_at' => ['sometimes', 'date'],
        ]);

        $validated['filed_at'] = $validated['filed_at'] ?? now()->toDateString();

        return response()->json(PublicRecord::create($validated), 201);
    }

    public function destroy(PublicRecord $publicRecord)
    {
        $publicRecord->delete();

        return response()->noContent();
    }
}
