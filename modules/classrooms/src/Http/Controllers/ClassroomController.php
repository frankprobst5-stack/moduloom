<?php

namespace Moduloom\Classrooms\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Moduloom\Classrooms\Models\Classroom;

class ClassroomController extends Controller
{
    public function index()
    {
        return Classroom::query()->withCount('students')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        return response()->json(Classroom::create($validated), 201);
    }

    public function destroy(Classroom $classroom)
    {
        $classroom->delete();

        return response()->noContent();
    }
}
