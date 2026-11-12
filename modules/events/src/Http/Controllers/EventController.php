<?php

namespace Moduloom\Events\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Moduloom\Events\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        return Event::query()->withCount('registrations')->orderBy('starts_at')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'starts_at' => ['required', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        return response()->json(Event::create($validated), 201);
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return response()->noContent();
    }
}
