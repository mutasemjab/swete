<?php

namespace App\Http\Controllers\Tenders;

use App\Http\Controllers\ModuleController;
use App\Models\Branch;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Material;
use App\Models\PriceQuote;
use App\Models\QuoteDeliveryTerm;
use App\Models\QuoteSupplyScope;
use App\Models\Tender;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
        $tender = $request->filled('tender_id') ? Tender::find($request->input('tender_id')) : null;

        return $this->moduleView('tenders.price-quotes.create', [
            ...$this->formOptions(),
            'priceQuote' => null,
            'tender'     => $tender,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        $priceQuote = PriceQuote::create([
            ...$validated,
            'number'     => PriceQuote::nextNumber(),
            'status'     => 'draft',
            'created_by' => $request->user()->id,
        ]);

        $this->syncItems($priceQuote, $validated['items']);

        return redirect()->route('price-quotes.show', $priceQuote)
            ->with('success', __('tenders.quote_added'));
    }

    public function show(PriceQuote $priceQuote)
    {
        $priceQuote->load([
            'customer', 'tender', 'creator', 'branch', 'currency', 'supplyScope', 'deliveryTerm',
            'items.material.unit',
        ]);

        return $this->moduleView('tenders.price-quotes.show', compact('priceQuote'));
    }

    public function edit(PriceQuote $priceQuote)
    {
        $priceQuote->load('items');

        return $this->moduleView('tenders.price-quotes.edit', [
            ...$this->formOptions(),
            'priceQuote' => $priceQuote,
            'tender'     => $priceQuote->tender,
        ]);
    }

    public function update(Request $request, PriceQuote $priceQuote)
    {
        $validated = $this->validated($request);

        $priceQuote->update($validated);

        $this->syncItems($priceQuote, $validated['items']);

        return redirect()->route('price-quotes.show', $priceQuote)
            ->with('success', __('tenders.quote_updated'));
    }

    /** Standalone, print-optimized 2-page A4 document — deliberately not wrapped in the app shell. */
    public function printDocument(PriceQuote $priceQuote)
    {
        $priceQuote->load([
            'branch', 'currency', 'customer', 'tender', 'supplyScope', 'deliveryTerm',
            'items.material.unit',
        ]);

        return view('tenders.price-quotes.print', compact('priceQuote'));
    }

    private function formOptions(): array
    {
        return [
            'customers'      => Customer::where('status', true)->orderBy('name')->get(),
            'materials'      => Material::where('status', true)->orderBy('name')->get(),
            'branches'       => Branch::where('status', true)->orderBy('name')->get(),
            'currencies'     => Currency::where('status', true)->orderBy('name')->get(),
            'supplyScopes'   => QuoteSupplyScope::where('status', true)->orderBy('name')->get(),
            'deliveryTerms'  => QuoteDeliveryTerm::where('status', true)->orderBy('name')->get(),
        ];
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'tender_id'                => ['nullable', 'exists:tenders,id'],
            'customer_id'               => ['required', 'exists:customers,id'],
            'date'                      => ['required', 'date'],
            'branch_id'                 => ['required', 'exists:branches,id'],
            'currency_id'               => ['required', 'exists:currencies,id'],
            'validity_weeks'            => ['nullable', 'integer', 'min:1'],
            'supply_scope_id'           => ['nullable', 'exists:quote_supply_scopes,id'],
            'delivery_term_id'          => ['nullable', 'exists:quote_delivery_terms,id'],
            'winching_included'         => ['boolean'],
            'sales_tax_included'        => ['boolean'],
            'customs_fees_included'     => ['boolean'],
            'include_boiler_note'       => ['boolean'],
            'included_work_scopes'      => ['nullable', 'array'],
            'included_work_scopes.*'    => ['string', 'in:' . implode(',', array_keys(PriceQuote::WORK_SCOPE_ITEMS))],
            'additional_terms'          => ['nullable', 'string'],
            'notes'                     => ['nullable', 'string'],
            'items'                     => ['required', 'array', 'min:1'],
            'items.*.material_id'       => ['required', 'exists:materials,id'],
            'items.*.quantity'          => ['required', 'numeric', 'min:0.001'],
            'items.*.unit_price'        => ['required', 'numeric', 'min:0'],
            'items.*.notes'             => ['nullable', 'array'],
            'items.*.notes.*'           => ['nullable', 'string', 'max:500'],
            'discount_type'             => ['required', 'in:amount,percent'],
            'discount_value'            => ['nullable', 'numeric', 'min:0', $request->input('discount_type') === 'percent' ? 'max:100' : 'max:999999999'],
        ]);

        $validated['discount_value'] = (float) ($validated['discount_value'] ?? 0);

        $subtotal = collect($validated['items'])->sum(fn ($item) => $item['quantity'] * $item['unit_price']);

        if ($validated['discount_type'] === 'amount' && $validated['discount_value'] > $subtotal) {
            throw ValidationException::withMessages(['discount_value' => __('tenders.quote_discount_exceeds')]);
        }

        $validated['winching_included']     = $request->boolean('winching_included');
        $validated['sales_tax_included']    = $request->boolean('sales_tax_included');
        $validated['customs_fees_included'] = $request->boolean('customs_fees_included');
        $validated['include_boiler_note']   = $request->boolean('include_boiler_note');

        // No checkboxes submitted at all (e.g. a non-JS fallback) shouldn't collapse to "everything excluded".
        $validated['included_work_scopes'] = $request->has('included_work_scopes')
            ? ($validated['included_work_scopes'] ?? [])
            : array_keys(PriceQuote::WORK_SCOPE_ITEMS);

        return $validated;
    }

    private function syncItems(PriceQuote $priceQuote, array $items): void
    {
        $priceQuote->items()->delete();

        foreach ($items as $item) {
            $notes = array_values(array_filter(array_map(fn ($note) => trim((string) $note), $item['notes'] ?? []), fn ($note) => $note !== ''));

            $priceQuote->items()->create([
                ...$item,
                'notes' => $notes ?: null,
                'total' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        $priceQuote->recalculateTotals();
    }
}
