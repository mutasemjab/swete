<?php

namespace App\Http\Controllers;

use App\Models\PriceAnalysis;
use App\Models\PriceAnalysisItem;
use App\Models\X8;
use App\Models\X8Item;
use Illuminate\Http\Request;

/**
 * "X8" — standalone module, same shape as every other module in this app (Price Quotes, Price
 * Analyses, ...): index() lists X8 HEADERS (one per tender-linked analysis, each with its own
 * `number`), show() opens one header's own page listing every item under it. Approve/reject acts
 * on a specific item (PriceAnalysisItem) within whatever header page it's reached from — this
 * module's own show() page, or the "Pending X8 Items" section on Projects Under Review. Rejecting
 * requires a new proposed required_discount_percent (a counter-offer — see
 * PriceAnalysisController::saveX8()'s sibling flow). Every decision archives the whole Price
 * Analysis first (PriceAnalysis::archiveCurrentVersion()), same revisioning rule as every other
 * edit to that analysis.
 */
class X8Controller extends ModuleController
{
    protected string $module = 'x8';

    public function index(Request $request)
    {
        $query = X8::query()
            ->whereHas('priceAnalysis', fn ($q) => $q->where('is_current', true)->where('linked_type', 'tender'))
            ->with(['priceAnalysis', 'items']);

        if ($request->filled('search')) {
            $query->where('number', 'like', '%' . $request->input('search') . '%');
        }

        $headers = $query->latest('id')->paginate(20)->withQueryString();

        $this->resolveLinkedTenders($headers->getCollection());

        return $this->moduleView('x8.index', compact('headers'));
    }

    /** One header's own page — every item under its analysis, with inline approve/reject. */
    public function show(X8 $x8)
    {
        $x8->load(['priceAnalysis.items.material', 'priceAnalysis.items.x8']);
        $this->resolveLinkedTenders(collect([$x8]));

        return $this->moduleView('x8.show', ['x8' => $x8]);
    }

    public function approve(Request $request, PriceAnalysisItem $item)
    {
        $analysis = $item->priceAnalysis;
        abort_unless($analysis->is_current, 404);

        $analysis->archiveCurrentVersion();

        $this->itemRow($analysis, $item, $request)->update(['approval_status' => 'approved']);

        return back()->with('success', __('x8.item_approved'));
    }

    public function reject(Request $request, PriceAnalysisItem $item)
    {
        $analysis = $item->priceAnalysis;
        abort_unless($analysis->is_current, 404);

        $validated = $request->validate([
            'required_discount_percent' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $analysis->archiveCurrentVersion();

        $this->itemRow($analysis, $item, $request)->update([
            'approval_status'           => 'pending',
            'required_discount_percent' => $validated['required_discount_percent'],
        ]);

        return back()->with('success', __('x8.item_rejected'));
    }

    /** Gets (or creates) this analysis's X8 header, then this item's own row under it. */
    private function itemRow(PriceAnalysis $analysis, PriceAnalysisItem $item, Request $request): X8Item
    {
        $header = X8::getOrCreateFor($analysis, $request->user()->id);

        return X8Item::firstOrCreate(['x8_id' => $header->id, 'price_analysis_item_id' => $item->id]);
    }

    /**
     * PriceAnalysis::linked() is a convention-based relation (branches on linked_type), not a real
     * Eloquent relation — eager-loading it via with() silently resolves to null, so it's set
     * manually per analysis instead (same fix already applied elsewhere for this relation).
     */
    private function resolveLinkedTenders($headers): void
    {
        $headers->each(function ($x8) {
            if ($x8->priceAnalysis) {
                $x8->priceAnalysis->setRelation('linked', $x8->priceAnalysis->linked()->first());
            }
        });
    }
}
