<?php

namespace Moduloom\PersonalDatabases\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Moduloom\PersonalDatabases\Models\PdCollection;
use Moduloom\PersonalDatabases\Models\PdRecord;

class RecordController extends Controller
{
    public function index(PdCollection $collection)
    {
        return $collection->records()->get();
    }

    public function store(Request $request, PdCollection $collection)
    {
        $validated = $request->validate(['data' => ['required', 'array']]);

        return $collection->records()->create(['data' => $validated['data']]);
    }

    public function destroy(PdRecord $record)
    {
        $record->delete();

        return response()->noContent();
    }
}
