<?php

namespace Moduloom\Classrooms\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Moduloom\Classrooms\Models\Classroom;

class StudentController extends Controller
{
    public function store(Request $request, Classroom $classroom)
    {
        $validated = $request->validate(['student_name' => ['required', 'string', 'max:255']]);

        return response()->json($classroom->students()->create($validated), 201);
    }
}
