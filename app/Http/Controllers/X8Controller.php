<?php

namespace App\Http\Controllers;

use App\Models\PriceAnalysisItem;
use App\Models\X8;
use Illuminate\Http\Request;

/**
 * "X8" — standalone module listing every CIAT-discount decision line across ALL tenders (not
 * scoped to one tender's own page, unlike the read-only table on tenders/show.blade.php). Lets a
 * reviewer approve a line as-is, or reject it with a new proposed required_discount_percent (a
 * counter-offer — see PriceAnalysisController::saveX8()'s sibling flow). Every decision archives
 * the whole Price Analysis first (PriceAnalysis::archiveCurrentVersion()), same revisioning rule
 * as every other edit to that analysis.
 */
class X8Controller extends ModuleController
{
    protected string $module = 'x8';

    public function index(Request $request)
    {
        $query = PriceAnalysisItem::query()
            ->whereHas('priceAnalysis', fn ($q) => $q->where('is_current', true)->where('linked_type', 'tender'))
            ->with(['x8', 'material', 'ciatDiscount', 'priceAnalysis']);

        if ($request->filled('status')) {
            $status = $request->input('status');
            $query->where(function ($q) use ($status) {
                if ($status === 'pending') {
                    $q->whereDoesntHave('x8')->orWhereHas('x8', fn ($q2) => $q2->where('approval_status', 'pending'));
                } else {
                    $q->whereHas('x8', fn ($q2) => $q2->where('approval_status', $status));
                }
            });
        }

        $items = $query->latest('id')->paginate(20)->withQueryString();

        // PriceAnalysis::linked() is a convention-based relation (branches on linked_type), not a
        // real Eloquent relation — eager-loading it via with() silently resolves to null, so it's
        // set manually per analysis instead (same fix already applied elsewhere for this relation).
        $items->getCollection()->each(function ($item) {
            if ($item->priceAnalysis) {
                $item->priceAnalysis->setRelation('linked', $item->priceAnalysis->linked()->first());
            }
        });

        return $this->moduleView('x8.index', compact('items'));
    }

    public function approve(PriceAnalysisItem $item)
    {
        $analysis = $item->priceAnalysis;
        abort_unless($analysis->is_current, 404);

        $analysis->archiveCurrentVersion();

        X8::updateOrCreate(
            ['price_analysis_item_id' => $item->id],
            ['approval_status' => 'approved'],
        );

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

        X8::updateOrCreate(
            ['price_analysis_item_id' => $item->id],
            [
                'approval_status'           => 'pending',
                'required_discount_percent' => $validated['required_discount_percent'],
            ],
        );

        return back()->with('success', __('x8.item_rejected'));
    }
}
