<?php

namespace App\Http\Controllers\Tenders;

use App\Http\Controllers\ModuleController;
use App\Models\Branch;
use App\Models\CiatDiscount;
use App\Models\Material;
use App\Models\PriceAnalysis;
use App\Models\PriceAnalysisItem;
use App\Models\Tender;
use App\Models\X8;
use App\Models\X8Item;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PriceAnalysisController extends ModuleController
{
    protected string $module = 'price_analyses';

    public function index(Request $request)
    {
        $query = PriceAnalysis::with(['branch', 'creator'])->where('is_current', true);

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->input('branch_id'));
        }

        $analyses = $query->latest()->paginate(20)->withQueryString();
        $branches = Branch::where('status', true)->orderBy('name')->get();

        return $this->moduleView('tenders.price-analyses.index', compact('analyses', 'branches'));
    }

    public function create(Request $request)
    {
        // Reached from a Tender's own "تحليل جديد" button (?tender_id=X) — the link is set here,
        // automatically, never via a picker on the analysis's own form.
        $tender = $request->filled('tender_id') ? Tender::find($request->input('tender_id')) : null;

        return $this->moduleView('tenders.price-analyses.create', [
            'analysis' => null,
            'tender'   => $tender,
            ...$this->formOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $branch    = Branch::findOrFail($validated['branch_id']);
        $linked    = $this->linkedFields($validated);

        $analysis = PriceAnalysis::create([
            'number'      => PriceAnalysis::nextNumber(),
            'branch_id'   => $branch->id,
            'tax_rate'    => $branch->ciat_tax_rate,
            'jd_rate'     => $branch->ciat_jd_rate,
            'with_tax'    => $request->boolean('with_tax'),
            'notes'       => $validated['notes'] ?? null,
            ...$linked,
            'created_by'  => $request->user()->id,
        ]);

        $this->syncItems($analysis, $validated['items'], (float) $branch->ciat_jd_rate);

        // Every tender-linked analysis gets its "X8" header (and number) right away, same as any
        // other entity in the system — not conjured up lazily on the first approve/reject/save.
        if ($analysis->linked_type === 'tender') {
            X8::getOrCreateFor($analysis, $request->user()->id);
        }

        return redirect()->route('price-analyses.show', $analysis)
            ->with('success', __('tenders.price_analysis_added'));
    }

    public function show(PriceAnalysis $priceAnalysis)
    {
        $priceAnalysis->load(['branch', 'creator', 'items.ciatDiscount', 'items.material', 'versions']);
        // linked() isn't a real polymorphic relation (same convention as Agreement::party()), so it
        // can't be eager-loaded with ->load() — set it individually instead.
        $priceAnalysis->setRelation('linked', $priceAnalysis->linked()->first());

        return $this->moduleView('tenders.price-analyses.show', ['analysis' => $priceAnalysis]);
    }

    public function edit(PriceAnalysis $priceAnalysis)
    {
        abort_unless($priceAnalysis->is_current, 404);
        $priceAnalysis->load('items');
        $priceAnalysis->setRelation('linked', $priceAnalysis->linked()->first());

        return $this->moduleView('tenders.price-analyses.edit', [
            'analysis' => $priceAnalysis,
            ...$this->formOptions(),
        ]);
    }

    public function update(Request $request, PriceAnalysis $priceAnalysis)
    {
        abort_unless($priceAnalysis->is_current, 404);

        $validated = $this->validated($request);
        $branch    = Branch::findOrFail($validated['branch_id']);
        $linked    = $this->linkedFields($validated);

        // Only re-snapshot the branch's tax/JD-rate defaults when the branch itself actually changes —
        // otherwise this analysis keeps whatever rates it was originally created with.
        $rates = $branch->id === $priceAnalysis->branch_id
            ? ['tax_rate' => $priceAnalysis->tax_rate, 'jd_rate' => $priceAnalysis->jd_rate]
            : ['tax_rate' => $branch->ciat_tax_rate, 'jd_rate' => $branch->ciat_jd_rate];

        // Freeze the pre-edit state into a new read-only row before touching anything — this also
        // moves the live row (this one, same id) forward onto its next revision number itself.
        $priceAnalysis->archiveCurrentVersion();

        $priceAnalysis->update([
            'branch_id'   => $branch->id,
            ...$rates,
            'with_tax'    => $request->boolean('with_tax'),
            'notes'       => $validated['notes'] ?? null,
            ...$linked,
        ]);

        $this->syncItems($priceAnalysis, $validated['items'], (float) $rates['jd_rate']);

        return redirect()->route('price-analyses.show', $priceAnalysis)
            ->with('success', __('tenders.price_analysis_updated'));
    }

    /** Read-only "X8" page: this analysis's header + every item's current decision. */
    public function showX8(PriceAnalysis $priceAnalysis)
    {
        $priceAnalysis->load(['branch', 'items.material', 'items.x8']);
        $this->loadLinkedTenderForX8($priceAnalysis);

        return $this->moduleView('tenders.price-analyses.x8-show', ['analysis' => $priceAnalysis]);
    }

    /** Dedicated edit form for the same "X8" header, with the editable fields. */
    public function editX8(PriceAnalysis $priceAnalysis)
    {
        abort_unless($priceAnalysis->is_current, 404);
        $priceAnalysis->load(['branch', 'items.material', 'items.x8']);
        $this->loadLinkedTenderForX8($priceAnalysis);

        return $this->moduleView('tenders.price-analyses.x8', ['analysis' => $priceAnalysis]);
    }

    /**
     * Saves the whole X8 form in one go — one header (X8) per analysis, one upsert per item row
     * (X8Item). Always archives the analysis first (same unconditional-on-every-save rule as
     * update() above), so every required-discount change is its own full revision, not just the
     * ones made through X8Controller::approve()/reject().
     */
    public function saveX8(Request $request, PriceAnalysis $priceAnalysis)
    {
        abort_unless($priceAnalysis->is_current, 404);

        $request->validate([
            'x8'                                  => ['nullable', 'array'],
            'x8.*.price_analysis_item_id'         => ['required', 'exists:price_analysis_items,id'],
            'x8.*.required_discount_percent'      => ['nullable', 'numeric', 'min:0', 'max:100'],
            'x8.*.notes'                          => ['nullable', 'string', 'max:1000'],
        ]);

        $priceAnalysis->archiveCurrentVersion();

        $header = X8::getOrCreateFor($priceAnalysis, $request->user()->id);

        foreach ($request->input('x8', []) as $row) {
            X8Item::updateOrCreate(
                ['x8_id' => $header->id, 'price_analysis_item_id' => $row['price_analysis_item_id']],
                [
                    'required_discount_percent' => $row['required_discount_percent'] ?? null,
                    'notes'                     => $row['notes'] ?? null,
                ],
            );
        }

        return redirect()->route('price-analyses.x8.show', $priceAnalysis)
            ->with('success', __('tenders.x8_saved'));
    }

    public function destroy(PriceAnalysis $priceAnalysis)
    {
        $priceAnalysis->delete();

        return redirect()->route('price-analyses.index')
            ->with('success', __('tenders.price_analysis_deleted'));
    }

    /**
     * linked() isn't a real relation (see show()/edit() above) — set manually. The attachments
     * card on both X8 pages is tender-scoped (general to the WHOLE X8 section of that tender, not
     * this one analysis), so it only renders when this analysis is actually linked to a tender.
     */
    private function loadLinkedTenderForX8(PriceAnalysis $priceAnalysis): void
    {
        $priceAnalysis->setRelation('linked', $priceAnalysis->linked()->first());

        if ($priceAnalysis->linked_type === 'tender' && $priceAnalysis->linked) {
            $priceAnalysis->linked->load('analysisAttachments');
        }
    }

    private function formOptions(): array
    {
        return [
            'branches'      => Branch::where('status', true)->orderBy('name')->get(),
            'ciatDiscounts' => CiatDiscount::where('status', true)->with('material')->get()
                ->sortBy(fn ($discount) => $discount->material?->localized_name)->values(),
            // Excludes CIAT-flagged materials — those belong exclusively to the CIAT path above,
            // never double-offered under "from warehouse" too.
            'materials'     => Material::where('status', true)->where('is_ciat', false)->orderBy('name')->get(),
        ];
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'branch_id'                  => ['required', 'exists:branches,id'],
            'with_tax'                   => ['boolean'],
            'notes'                      => ['nullable', 'string'],
            'linked_type'                => ['nullable', 'in:tender,price_quote,project'],
            'linked_id'                  => ['nullable', 'required_with:linked_type', 'integer'],
            'items'                      => ['required', 'array', 'min:1'],
            'items.*.source_type'        => ['required', 'in:ciat,warehouse'],
            'items.*.ciat_discount_id'   => ['nullable', 'exists:ciat_discounts,id'],
            'items.*.material_id'        => ['nullable', 'exists:materials,id'],
            'items.*.ciat_model'         => ['nullable', 'string', 'max:100'],
            'items.*.quantity'           => ['required', 'numeric', 'min:0.001'],
            'items.*.list_price'         => ['required', 'numeric', 'min:0'],
            'items.*.profit_percent'     => ['required', 'numeric', 'min:0'],
            'items.*.shipping'           => ['nullable', 'numeric', 'min:0'],
        ]);

        // Each line's required fields depend on ITS OWN source_type — not expressible as a single
        // static rule once source_type lives per-item, so it's enforced here instead.
        foreach ($validated['items'] as $index => $item) {
            if ($item['source_type'] === 'ciat' && empty($item['ciat_discount_id'])) {
                throw ValidationException::withMessages(["items.{$index}.ciat_discount_id" => __('tenders.analysis_item_ciat_required')]);
            }
            if ($item['source_type'] === 'ciat' && empty($item['ciat_model'])) {
                throw ValidationException::withMessages(["items.{$index}.ciat_model" => __('tenders.analysis_item_ciat_model_required')]);
            }
            if ($item['source_type'] === 'warehouse' && empty($item['material_id'])) {
                throw ValidationException::withMessages(["items.{$index}.material_id" => __('tenders.analysis_item_material_required')]);
            }
        }

        return $validated;
    }

    /** `linked_id` is only ever meaningful alongside a `linked_type` — otherwise always null, never a stray leftover id. */
    private function linkedFields(array $validated): array
    {
        return [
            'linked_type' => $validated['linked_type'] ?? null,
            'linked_id'   => ($validated['linked_type'] ?? null) ? $validated['linked_id'] : null,
        ];
    }

    private function syncItems(PriceAnalysis $analysis, array $items, float $jdRate): void
    {
        // required_discount_percent/notes are set independently, later, from the Tender page's
        // own quick-edit table (see TenderController::updateAnalysisItem()) — never through this
        // form. Since every save here deletes and recreates all items from scratch, preserve those
        // two fields across the rebuild by matching old->new items positionally (best-effort: exact
        // when the line count/order is unchanged, lost only if lines are added/removed/reordered).
        $oldPreserved = $analysis->items()->orderBy('id')->get(['required_discount_percent', 'notes'])->values();

        $analysis->items()->delete();

        foreach ($items as $index => $item) {
            if ($item['source_type'] === 'warehouse') {
                $materialId       = $item['material_id'];
                $discountPercent  = 0.0;
                $minProfitPercent = 0.0;
                $ciatDiscountId   = null;
                $ciatModel        = $item['ciat_model'] ?? null;
            } else {
                $discount         = CiatDiscount::findOrFail($item['ciat_discount_id']);
                $materialId       = $discount->material_id;
                $discountPercent  = (float) $discount->discount_percent;
                $minProfitPercent = (float) ($discount->min_profit_percent ?? 0);
                $ciatDiscountId   = $discount->id;
                $ciatModel        = $item['ciat_model'];
            }

            $computed = PriceAnalysisItem::calculate(
                (float) $item['list_price'],
                $discountPercent,
                $minProfitPercent,
                (float) $item['profit_percent'],
                (float) $item['quantity'],
                $jdRate,
            );

            $analysis->items()->create([
                'source_type'        => $item['source_type'],
                'ciat_discount_id'   => $ciatDiscountId,
                'material_id'        => $materialId,
                'ciat_model'         => $ciatModel,
                'quantity'           => $item['quantity'],
                'list_price'         => $item['list_price'],
                'discount_percent'   => $discountPercent,
                'min_profit_percent' => $minProfitPercent,
                'profit_percent'     => $item['profit_percent'],
                'shipping'           => $item['shipping'] ?? 0,
                'required_discount_percent' => $oldPreserved->get($index)?->required_discount_percent,
                'notes'              => $oldPreserved->get($index)?->notes,
                ...$computed,
            ]);
        }
    }
}
