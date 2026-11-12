<?php

namespace Moduloom\Assignments\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Moduloom\Assignments\Models\Assignment;

class AssignmentController extends Controller
{
    public function index()
    {
        return Assignment::query()->with('classroom')->withCount('submissions')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
        ]);

        return response()->json(Assignment::create($validated), 201);
    }

    public function destroy(Assignment $assignment)
    {
        $assignment->delete();

        return response()->noContent();
    }
}
