<?php

namespace Moduloom\Assignments\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Moduloom\Assignments\Models\Assignment;

class SubmissionController extends Controller
{
    public function store(Request $request, Assignment $assignment)
    {
        $validated = $request->validate([
            'student_name' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
        ]);

        return response()->json($assignment->submissions()->create($validated), 201);
    }
}
