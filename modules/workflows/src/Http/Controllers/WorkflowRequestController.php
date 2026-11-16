<?php

namespace Moduloom\Workflows\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Moduloom\Workflows\Models\WorkflowRequest;

class WorkflowRequestController extends Controller
{
    public function index()
    {
        return WorkflowRequest::query()->latest()->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'requested_by' => ['nullable', 'string', 'max:255'],
        ]);

        return response()->json(WorkflowRequest::create($validated), 201);
    }

    public function update(Request $request, WorkflowRequest $workflowRequest)
    {
        $validated = $request->validate(['status' => ['required', 'in:pending,approved,rejected']]);

        $workflowRequest->update($validated);

        return $workflowRequest;
    }

    /**
     * workflows.view's "submit and view own requests" — the
     * requester's own account, not whatever name they'd type in.
     */
    public function mine(Request $request)
    {
        return WorkflowRequest::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();
    }

    public function storeMine(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $workflowRequest = WorkflowRequest::create([
            ...$validated,
            'user_id' => $request->user()->id,
            'requested_by' => $request->user()->name,
        ]);

        return response()->json($workflowRequest, 201);
    }
}
