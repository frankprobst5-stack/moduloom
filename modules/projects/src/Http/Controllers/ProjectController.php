<?php

namespace Moduloom\Projects\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Moduloom\Projects\Models\Project;

class ProjectController extends Controller
{
    public function index()
    {
        return Project::query()->withCount('tasks')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        return response()->json(Project::create($validated), 201);
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return response()->noContent();
    }
}
