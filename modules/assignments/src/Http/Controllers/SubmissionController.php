<?php

namespace Moduloom\Assignments\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Moduloom\Assignments\Models\Assignment;
use Moduloom\Assignments\Models\Submission;

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

    /**
     * assignments.view's "view and submit own assignments" — the
     * student's own account, not whatever name they'd otherwise type.
     */
    public function storeMine(Request $request, Assignment $assignment)
    {
        $validated = $request->validate([
            'content' => ['nullable', 'string'],
        ]);

        $submission = $assignment->submissions()->create([
            ...$validated,
            'user_id' => $request->user()->id,
            'student_name' => $request->user()->name,
        ]);

        return response()->json($submission, 201);
    }

    public function mine(Request $request)
    {
        return Submission::query()
            ->where('user_id', $request->user()->id)
            ->with('assignment')
            ->latest()
            ->get();
    }
}
