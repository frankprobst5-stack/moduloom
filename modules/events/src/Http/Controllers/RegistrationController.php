<?php

namespace Moduloom\Events\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Moduloom\Events\Models\Event;

class RegistrationController extends Controller
{
    public function store(Request $request, Event $event)
    {
        $validated = $request->validate([
            'attendee_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
        ]);

        return response()->json($event->registrations()->create($validated), 201);
    }
}
