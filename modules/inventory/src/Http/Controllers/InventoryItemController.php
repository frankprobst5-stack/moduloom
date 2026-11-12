<?php

namespace Moduloom\Inventory\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Moduloom\Inventory\Models\InventoryItem;

class InventoryItemController extends Controller
{
    public function index()
    {
        return InventoryItem::query()->orderBy('name')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:100'],
            'quantity' => ['required', 'integer', 'min:0'],
            'reorder_threshold' => ['sometimes', 'integer', 'min:0'],
        ]);

        return response()->json(InventoryItem::create($validated), 201);
    }

    public function update(Request $request, InventoryItem $item)
    {
        $validated = $request->validate([
            'quantity' => ['sometimes', 'integer', 'min:0'],
        ]);

        $item->update($validated);

        return $item;
    }

    public function destroy(InventoryItem $item)
    {
        $item->delete();

        return response()->noContent();
    }
}
