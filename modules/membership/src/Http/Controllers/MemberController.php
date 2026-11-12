<?php

namespace Moduloom\Membership\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Moduloom\Membership\Models\Member;

class MemberController extends Controller
{
    public function index()
    {
        return Member::query()->latest()->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'tier' => ['sometimes', 'in:basic,premium'],
            'joined_at' => ['sometimes', 'date'],
        ]);

        $validated['joined_at'] = $validated['joined_at'] ?? now()->toDateString();

        return response()->json(Member::create($validated), 201);
    }

    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:active,expired'],
        ]);

        $member->update($validated);

        return $member;
    }

    public function destroy(Member $member)
    {
        $member->delete();

        return response()->noContent();
    }
}
