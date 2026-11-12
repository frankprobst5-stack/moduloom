<?php

namespace Moduloom\PersonalDatabases\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Moduloom\PersonalDatabases\Models\PdCollection;

class CollectionController extends Controller
{
    public function index()
    {
        return PdCollection::query()->withCount('records')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate(['name' => ['required', 'string', 'max:255']]);

        return PdCollection::create($validated);
    }

    public function destroy(PdCollection $collection)
    {
        $collection->delete();

        return response()->noContent();
    }
}
