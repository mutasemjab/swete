<?php

namespace App\Http\Controllers\Tenders;

use App\Http\Controllers\ModuleController;
use App\Models\Customer;
use App\Models\Material;
use App\Models\PriceQuote;
use App\Models\Tender;
use Illuminate\Http\Request;

class PriceQuoteController extends ModuleController
{
    protected string $module = 'tenders';

    public function index(Request $request)
    {
        $query = PriceQuote::with(['customer', 'tender']);

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }

        $priceQuotes = $query->latest()->paginate(20)->withQueryString();
        $customers   = Customer::where('status', true)->orderBy('name')->get();

        return $this->moduleView('tenders.price-quotes.index', compact('priceQuotes', 'customers'));
    }

    public function create(Request $request)
    {
        $customers = Customer::where('status', true)->orderBy('name')->get();
        $materials = Material::where('status', true)->orderBy('name')->get();
        $tender    = $request->filled('tender_id') ? Tender::find($request->input('tender_id')) : null;

        return $this->moduleView('tenders.price-quotes.create', compact('customers', 'materials', 'tender'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tender_id'              => ['nullable', 'exists:tenders,id'],
            'customer_id'            => ['required', 'exists:customers,id'],
            'date'                   => ['required', 'date'],
            'notes'                  => ['nullable', 'string'],
            'items'                  => ['required', 'array', 'min:1'],
            'items.*.material_id'    => ['required', 'exists:materials,id'],
            'items.*.quantity'       => ['required', 'numeric', 'min:0.001'],
            'items.*.unit_price'     => ['required', 'numeric', 'min:0'],
        ]);

        $priceQuote = PriceQuote::create([
            'number'      => PriceQuote::nextNumber(),
            'tender_id'   => $validated['tender_id'] ?? null,
            'customer_id' => $validated['customer_id'],
            'date'        => $validated['date'],
            'status'      => 'draft',
            'notes'       => $validated['notes'] ?? null,
            'created_by'  => $request->user()->id,
        ]);

        foreach ($validated['items'] as $item) {
            $priceQuote->items()->create([
                ...$item,
                'total' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        $priceQuote->recalculateTotals();

        return redirect()->route('price-quotes.show', $priceQuote)
            ->with('success', __('tenders.quote_added'));
    }

    public function show(PriceQuote $priceQuote)
    {
        $priceQuote->load(['customer', 'tender', 'creator', 'items.material.unit']);

        return $this->moduleView('tenders.price-quotes.show', compact('priceQuote'));
    }
}
