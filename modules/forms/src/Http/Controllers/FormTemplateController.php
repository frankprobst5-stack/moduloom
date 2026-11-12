<?php

namespace Moduloom\Forms\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Moduloom\Forms\Models\FormTemplate;

class FormTemplateController extends Controller
{
    public function index()
    {
        return FormTemplate::query()->withCount('submissions')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        return response()->json(FormTemplate::create($validated), 201);
    }

    public function destroy(FormTemplate $formTemplate)
    {
        $formTemplate->delete();

        return response()->noContent();
    }
}
