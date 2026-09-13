<?php

namespace App\Http\Controllers\Tenders;

use App\Http\Controllers\ModuleController;
use App\Models\QuoteDeliveryTerm;
use Illuminate\Http\Request;

/** Dynamic/manageable list of price-quote delivery terms (e.g. "CFR Aqaba Port") — "add anything in the future" without code changes. */
class QuoteDeliveryTermController extends ModuleController
{
    protected string $module = 'tenders';

    public function index()
    {
        $terms = QuoteDeliveryTerm::orderBy('name')->get();
        return $this->moduleView('tenders.quote-delivery-terms.index', compact('terms'));
    }

    public function create()
    {
        return $this->moduleView('tenders.quote-delivery-terms.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        QuoteDeliveryTerm::create([
            ...$validated,
            'status' => $request->boolean('status', true),
        ]);

        return redirect()->route('quote-delivery-terms.index')
            ->with('success', __('tenders.quote_delivery_term_added'));
    }

    public function edit(QuoteDeliveryTerm $quoteDeliveryTerm)
    {
        return $this->moduleView('tenders.quote-delivery-terms.edit', ['term' => $quoteDeliveryTerm]);
    }

    public function update(Request $request, QuoteDeliveryTerm $quoteDeliveryTerm)
    {
        $validated = $this->validated($request);

        $quoteDeliveryTerm->update([
            ...$validated,
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('quote-delivery-terms.index')
            ->with('success', __('tenders.quote_delivery_term_updated'));
    }

    public function destroy(QuoteDeliveryTerm $quoteDeliveryTerm)
    {
        if ($quoteDeliveryTerm->priceQuotes()->exists()) {
            return back()->with('error', __('tenders.quote_delivery_term_in_use'));
        }

        $quoteDeliveryTerm->delete();

        return redirect()->route('quote-delivery-terms.index')
            ->with('success', __('tenders.quote_delivery_term_deleted'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'name_en' => ['nullable', 'string', 'max:100'],
        ]);
    }
}
