<?php

namespace Moduloom\Applications\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Moduloom\Applications\Models\Application;

class ApplicationController extends Controller
{
    public function index()
    {
        return Application::query()->latest()->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'applicant_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'application_type' => ['nullable', 'string', 'max:255'],
        ]);

        return response()->json(Application::create($validated), 201);
    }

    public function update(Request $request, Application $application)
    {
        $validated = $request->validate(['status' => ['required', 'in:submitted,under_review,approved,denied']]);

        $application->update($validated);

        return $application;
    }
}
