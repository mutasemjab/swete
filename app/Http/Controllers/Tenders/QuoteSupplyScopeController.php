<?php

namespace App\Http\Controllers\Tenders;

use App\Http\Controllers\ModuleController;
use App\Models\QuoteSupplyScope;
use Illuminate\Http\Request;

/** Dynamic/manageable list of price-quote "scope of supply" options — "add anything in the future" without code changes. */
class QuoteSupplyScopeController extends ModuleController
{
    protected string $module = 'tenders';

    public function index()
    {
        $scopes = QuoteSupplyScope::orderBy('name')->get();
        return $this->moduleView('tenders.quote-supply-scopes.index', compact('scopes'));
    }

    public function create()
    {
        return $this->moduleView('tenders.quote-supply-scopes.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        QuoteSupplyScope::create([
            ...$validated,
            'status' => $request->boolean('status', true),
        ]);

        return redirect()->route('quote-supply-scopes.index')
            ->with('success', __('tenders.quote_supply_scope_added'));
    }

    public function edit(QuoteSupplyScope $quoteSupplyScope)
    {
        return $this->moduleView('tenders.quote-supply-scopes.edit', ['scope' => $quoteSupplyScope]);
    }

    public function update(Request $request, QuoteSupplyScope $quoteSupplyScope)
    {
        $validated = $this->validated($request);

        $quoteSupplyScope->update([
            ...$validated,
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('quote-supply-scopes.index')
            ->with('success', __('tenders.quote_supply_scope_updated'));
    }

    public function destroy(QuoteSupplyScope $quoteSupplyScope)
    {
        if ($quoteSupplyScope->priceQuotes()->exists()) {
            return back()->with('error', __('tenders.quote_supply_scope_in_use'));
        }

        $quoteSupplyScope->delete();

        return redirect()->route('quote-supply-scopes.index')
            ->with('success', __('tenders.quote_supply_scope_deleted'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'name_en' => ['nullable', 'string', 'max:100'],
        ]);
    }
}
