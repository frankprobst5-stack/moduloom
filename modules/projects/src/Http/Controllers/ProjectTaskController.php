<?php

namespace Moduloom\Projects\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Moduloom\Projects\Models\Project;

class ProjectTaskController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'assignee_name' => ['nullable', 'string', 'max:255'],
        ]);

        return response()->json($project->tasks()->create($validated), 201);
    }
}
