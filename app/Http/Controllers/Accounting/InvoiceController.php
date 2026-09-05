<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\ModuleController;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\InvoiceType;
use App\Models\Party;
use Illuminate\Http\Request;

class InvoiceController extends ModuleController
{
    protected string $module = 'accounting';

    public function index(Request $request)
    {
        $query = Invoice::with(['invoiceType', 'party']);

        if ($request->filled('invoice_type_id')) {
            $query->where('invoice_type_id', $request->input('invoice_type_id'));
        }

        $invoices     = $query->latest()->paginate(20)->withQueryString();
        $invoiceTypes = InvoiceType::where('status', true)->orderBy('name')->get();

        return $this->moduleView('accounting.invoices.index', compact('invoices', 'invoiceTypes'));
    }

    public function create()
    {
        $invoiceTypes = InvoiceType::where('status', true)->orderBy('name')->get();
        $parties      = Party::where('status', true)->orderBy('name')->get();
        $currencies   = Currency::where('status', true)->orderBy('name')->get();

        return $this->moduleView('accounting.invoices.create', compact('invoiceTypes', 'parties', 'currencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_type_id'        => ['required', 'exists:invoice_types,id'],
            'party_id'                => ['required', 'exists:parties,id'],
            'date'                    => ['required', 'date'],
            'due_date'                => ['nullable', 'date'],
            'currency_id'             => ['nullable', 'exists:currencies,id'],
            'notes'                   => ['nullable', 'string'],
            'items'                   => ['required', 'array', 'min:1'],
            'items.*.description'     => ['required', 'string', 'max:255'],
            'items.*.quantity'        => ['required', 'numeric', 'min:0.001'],
            'items.*.unit_price'      => ['required', 'numeric', 'min:0'],
        ]);

        $type = InvoiceType::findOrFail($validated['invoice_type_id']);

        $party = Party::findOrFail($validated['party_id']);
        if ($party->type !== $type->party_type) {
            return back()->withErrors(['party_id' => __('accounting.party_type_mismatch')])->withInput();
        }

        $invoice = Invoice::create([
            'invoice_type_id' => $type->id,
            'party_id'        => $party->id,
            'number'          => Invoice::nextNumber($type),
            'date'            => $validated['date'],
            'due_date'        => $validated['due_date'] ?? null,
            'currency_id'     => $validated['currency_id'] ?? null,
            'notes'           => $validated['notes'] ?? null,
            'status'          => 'draft',
            'created_by'      => $request->user()->id,
        ]);

        foreach ($validated['items'] as $item) {
            $invoice->items()->create([
                ...$item,
                'total' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        $invoice->recalculateTotals();

        return redirect()->route('accounting.invoices.show', $invoice)
            ->with('success', __('accounting.invoice_added'));
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['invoiceType', 'party', 'currency', 'creator', 'items']);
        return $this->moduleView('accounting.invoices.show', compact('invoice'));
    }
}
