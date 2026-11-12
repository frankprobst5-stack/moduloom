<?php

namespace Moduloom\Forms\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Moduloom\Forms\Models\FormTemplate;

class FormSubmissionController extends Controller
{
    public function index(FormTemplate $formTemplate)
    {
        return $formTemplate->submissions()->get();
    }

    public function store(Request $request, FormTemplate $formTemplate)
    {
        $validated = $request->validate(['data' => ['required', 'array']]);

        return response()->json($formTemplate->submissions()->create(['data' => $validated['data']]), 201);
    }
}
