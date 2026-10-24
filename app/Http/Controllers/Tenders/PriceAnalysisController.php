<?php

namespace App\Http\Controllers\Tenders;

use App\Http\Controllers\ModuleController;
use App\Models\Branch;
use App\Models\CiatDiscount;
use App\Models\Material;
use App\Models\PriceAnalysis;
use App\Models\PriceAnalysisItem;
use App\Models\PriceQuote;
use App\Models\Project;
use Illuminate\Http\Request;

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

    public function create()
    {
        return $this->moduleView('tenders.price-analyses.create', [
            'analysis' => null,
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
            'source_type' => $validated['source_type'],
            'branch_id'   => $branch->id,
            'tax_rate'    => $branch->ciat_tax_rate,
            'jd_rate'     => $branch->ciat_jd_rate,
            'with_tax'    => $request->boolean('with_tax'),
            'notes'       => $validated['notes'] ?? null,
            ...$linked,
            'created_by'  => $request->user()->id,
        ]);

        $this->syncItems($analysis, $validated['items'], (float) $branch->ciat_jd_rate);

        return redirect()->route('price-analyses.show', $analysis)
            ->with('success', __('tenders.price_analysis_added'));
    }

    public function show(PriceAnalysis $priceAnalysis)
    {
        $priceAnalysis->load(['branch', 'creator', 'items.ciatDiscount', 'items.material', 'versions', 'linked']);

        return $this->moduleView('tenders.price-analyses.show', ['analysis' => $priceAnalysis]);
    }

    public function edit(PriceAnalysis $priceAnalysis)
    {
        abort_unless($priceAnalysis->is_current, 404);
        $priceAnalysis->load('items');

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
            'source_type' => $validated['source_type'],
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

    public function destroy(PriceAnalysis $priceAnalysis)
    {
        $priceAnalysis->delete();

        return redirect()->route('price-analyses.index')
            ->with('success', __('tenders.price_analysis_deleted'));
    }

    private function formOptions(): array
    {
        return [
            'branches'      => Branch::where('status', true)->orderBy('name')->get(),
            'ciatDiscounts' => CiatDiscount::where('status', true)->with('material')->get()
                ->sortBy(fn ($discount) => $discount->material?->localized_name)->values(),
            'materials'     => Material::where('status', true)->orderBy('name')->get(),
            'priceQuotes'   => PriceQuote::where('is_current', true)->with('customer')->latest()->get(),
            'projects'      => Project::where('is_current', true)->with('customer')->latest()->get(),
        ];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'source_type'                => ['required', 'in:ciat,warehouse'],
            'branch_id'                  => ['required', 'exists:branches,id'],
            'with_tax'                   => ['boolean'],
            'notes'                      => ['nullable', 'string'],
            'linked_type'                => ['nullable', 'in:price_quote,project'],
            'linked_id'                  => ['nullable', 'required_with:linked_type', 'integer'],
            'items'                      => ['required', 'array', 'min:1'],
            'items.*.ciat_discount_id'   => ['nullable', 'required_if:source_type,ciat', 'exists:ciat_discounts,id'],
            'items.*.material_id'        => ['nullable', 'required_if:source_type,warehouse', 'exists:materials,id'],
            'items.*.ciat_model'         => ['nullable', 'required_if:source_type,ciat', 'string', 'max:100'],
            'items.*.quantity'           => ['required', 'numeric', 'min:0.001'],
            'items.*.list_price'         => ['required', 'numeric', 'min:0'],
            'items.*.profit_percent'     => ['required', 'numeric'],
            'items.*.shipping'           => ['nullable', 'numeric', 'min:0'],
        ]);
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
        $analysis->items()->delete();

        foreach ($items as $item) {
            if ($analysis->source_type === 'warehouse') {
                $materialId      = $item['material_id'];
                $discountPercent = 0.0;
                $ciatDiscountId  = null;
                $ciatModel       = $item['ciat_model'] ?? null;
            } else {
                $discount         = CiatDiscount::findOrFail($item['ciat_discount_id']);
                $materialId       = $discount->material_id;
                $discountPercent  = (float) $discount->discount_percent;
                $ciatDiscountId   = $discount->id;
                $ciatModel        = $item['ciat_model'];
            }

            $computed = PriceAnalysisItem::calculate(
                (float) $item['list_price'],
                $discountPercent,
                (float) $item['profit_percent'],
                (float) $item['quantity'],
                $jdRate,
            );

            $analysis->items()->create([
                'ciat_discount_id' => $ciatDiscountId,
                'material_id'      => $materialId,
                'ciat_model'       => $ciatModel,
                'quantity'         => $item['quantity'],
                'list_price'       => $item['list_price'],
                'discount_percent' => $discountPercent,
                'profit_percent'   => $item['profit_percent'],
                'shipping'         => $item['shipping'] ?? 0,
                ...$computed,
            ]);
        }
    }
}
