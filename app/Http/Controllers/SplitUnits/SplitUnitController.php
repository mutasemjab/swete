<?php

namespace App\Http\Controllers\SplitUnits;

use App\Http\Controllers\ModuleController;
use App\Models\Branch;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceType;
use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\MaterialStock;
use App\Models\QuoteDeliveryTerm;
use App\Models\QuoteSupplyScope;
use App\Models\SplitUnit;
use App\Models\SplitUnitItem;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SplitUnitController extends ModuleController
{
    protected string $module = 'split_units';

    public function index(Request $request)
    {
        $query = SplitUnit::with(['customer', 'assignee', 'invoice']);

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->input('assigned_to'));
        }

        $splitUnits = $query->latest()->paginate(20)->withQueryString();

        return $this->moduleView('split-units.index', [
            'splitUnits' => $splitUnits,
            'customers'  => Customer::where('status', true)->orderBy('name')->get(),
            'employees'  => User::where('status', true)->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return $this->moduleView('split-units.create', [
            ...$this->formOptions(),
            ...$this->historyData(),
            'splitUnit' => null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        $splitUnit = SplitUnit::create([
            ...$validated,
            'number'     => SplitUnit::nextNumber(),
            'status'     => 'draft',
            'created_by' => $request->user()->id,
        ]);

        $this->syncItems($splitUnit, $validated['items']);

        return redirect()->route('split-units.show', $splitUnit)
            ->with('success', __('split_units.quote_added'));
    }

    public function show(SplitUnit $splitUnit)
    {
        $splitUnit->load([
            'customer', 'creator', 'branch', 'currency', 'supplyScope', 'deliveryTerm',
            'items.material.unit', 'assignee', 'invoice',
        ]);

        return $this->moduleView('split-units.show', compact('splitUnit'));
    }

    public function edit(SplitUnit $splitUnit)
    {
        $splitUnit->load('items');

        return $this->moduleView('split-units.edit', [
            ...$this->formOptions(),
            ...$this->historyData($splitUnit),
            'splitUnit' => $splitUnit,
        ]);
    }

    public function update(Request $request, SplitUnit $splitUnit)
    {
        $validated = $this->validated($request);

        $splitUnit->update($validated);

        $this->syncItems($splitUnit, $validated['items']);

        return redirect()->route('split-units.show', $splitUnit)
            ->with('success', __('split_units.quote_updated'));
    }

    public function destroy(SplitUnit $splitUnit)
    {
        $splitUnit->delete();

        return redirect()->route('split-units.index')
            ->with('success', __('split_units.quote_deleted'));
    }

    public function printDocument(SplitUnit $splitUnit)
    {
        $splitUnit->load([
            'branch', 'currency', 'customer', 'supplyScope', 'deliveryTerm',
            'items.material.unit',
        ]);

        return view('split-units.print', compact('splitUnit'));
    }

    private function formOptions(): array
    {
        return [
            'customers'          => Customer::where('status', true)->orderBy('name')->get(),
            'materials'          => Material::where('status', true)->orderBy('name')->get(),
            'branches'           => Branch::where('status', true)->orderBy('name')->get(),
            'currencies'         => Currency::where('status', true)->orderBy('name')->get(),
            'supplyScopes'       => QuoteSupplyScope::where('status', true)->orderBy('name')->get(),
            'deliveryTerms'      => QuoteDeliveryTerm::where('status', true)->orderBy('name')->get(),
            'materialCategories' => MaterialCategory::orderBy('name')->get(),
            'units'              => Unit::where('status', true)->orderBy('name')->get(),
        ];
    }

    private function historyData(?SplitUnit $current = null): array
    {
        $unitsQuery = SplitUnit::latest();

        if ($current) {
            $unitsQuery->where('id', '!=', $current->id);
        }

        $customerHistory = $unitsQuery->get(['id', 'customer_id', 'number', 'date', 'total', 'currency_id'])
            ->groupBy('customer_id')
            ->map(fn ($units) => $units->take(8)->map(fn ($unit) => [
                'number'   => $unit->number,
                'date'     => $unit->date->format('Y-m-d'),
                'total'    => (float) $unit->total,
                'currency' => $unit->currency?->code,
                'url'      => route('split-units.show', $unit->id),
            ])->values());

        $itemsQuery = SplitUnitItem::query()->latest('id');

        if ($current) {
            $itemsQuery->where('split_unit_id', '!=', $current->id);
        }

        $materialPriceHistory = $itemsQuery->get(['id', 'split_unit_id', 'material_id', 'quantity', 'unit_price'])
            ->filter(fn ($item) => $item->splitUnit !== null)
            ->groupBy('material_id')
            ->map(fn ($items) => $items->sortByDesc(fn ($item) => $item->splitUnit->date)->take(8)->map(fn ($item) => [
                'number'     => $item->splitUnit->number,
                'date'       => $item->splitUnit->date->format('Y-m-d'),
                'quantity'   => (float) $item->quantity,
                'unit_price' => (float) $item->unit_price,
            ])->values());

        $materialStock = MaterialStock::selectRaw('material_id, SUM(quantity) as qty')
            ->groupBy('material_id')
            ->pluck('qty', 'material_id');

        return [
            'customerHistory'     => $customerHistory,
            'materialPriceHistory' => $materialPriceHistory,
            'materialStock'       => $materialStock,
        ];
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
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
            'included_work_scopes.*'    => ['string', 'in:' . implode(',', array_keys(SplitUnit::WORK_SCOPE_ITEMS))],
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
            throw ValidationException::withMessages(['discount_value' => __('split_units.quote_discount_exceeds')]);
        }

        $validated['winching_included']     = $request->boolean('winching_included');
        $validated['sales_tax_included']    = $request->boolean('sales_tax_included');
        $validated['customs_fees_included'] = $request->boolean('customs_fees_included');
        $validated['include_boiler_note']   = $request->boolean('include_boiler_note');

        $validated['included_work_scopes'] = $request->has('included_work_scopes')
            ? ($validated['included_work_scopes'] ?? [])
            : array_keys(SplitUnit::WORK_SCOPE_ITEMS);

        return $validated;
    }

    private function syncItems(SplitUnit $splitUnit, array $items): void
    {
        $splitUnit->items()->delete();

        foreach ($items as $item) {
            $notes = array_values(array_filter(array_map(fn ($note) => trim((string) $note), $item['notes'] ?? []), fn ($note) => $note !== ''));

            $splitUnit->items()->create([
                ...$item,
                'notes' => $notes ?: null,
                'total' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        $splitUnit->recalculateTotals();
    }
}
