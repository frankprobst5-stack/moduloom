<?php

namespace Moduloom\Invoicing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Moduloom\Invoicing\Models\Invoice;

class InvoiceController extends Controller
{
    public function index()
    {
        return Invoice::query()->latest()->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'crm_contact_id' => ['nullable', 'exists:crm_contacts,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'amount_cents' => ['required', 'integer', 'min:0'],
            'status' => ['sometimes', 'in:draft,sent,paid'],
        ]);

        return response()->json(Invoice::create($validated), 201);
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:draft,sent,paid'],
        ]);

        $invoice->update($validated);

        return $invoice;
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return response()->noContent();
    }
}
