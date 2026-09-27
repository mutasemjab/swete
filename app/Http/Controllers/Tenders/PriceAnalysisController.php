<?php

namespace App\Http\Controllers\Tenders;

use App\Http\Controllers\ModuleController;
use App\Models\Branch;
use App\Models\CiatDiscount;
use App\Models\PriceAnalysis;
use App\Models\PriceAnalysisItem;
use Illuminate\Http\Request;

class PriceAnalysisController extends ModuleController
{
    protected string $module = 'tenders';

    public function index(Request $request)
    {
        $query = PriceAnalysis::with(['branch', 'creator']);

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

        $analysis = PriceAnalysis::create([
            'number'     => PriceAnalysis::nextNumber(),
            'branch_id'  => $branch->id,
            'tax_rate'   => $branch->ciat_tax_rate,
            'jd_rate'    => $branch->ciat_jd_rate,
            'with_tax'   => $request->boolean('with_tax'),
            'notes'      => $validated['notes'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        $this->syncItems($analysis, $validated['items'], (float) $branch->ciat_jd_rate);

        return redirect()->route('price-analyses.show', $analysis)
            ->with('success', __('tenders.price_analysis_added'));
    }

    public function show(PriceAnalysis $priceAnalysis)
    {
        $priceAnalysis->load(['branch', 'creator', 'items.ciatDiscount', 'items.material']);

        return $this->moduleView('tenders.price-analyses.show', ['analysis' => $priceAnalysis]);
    }

    public function edit(PriceAnalysis $priceAnalysis)
    {
        $priceAnalysis->load('items');

        return $this->moduleView('tenders.price-analyses.edit', [
            'analysis' => $priceAnalysis,
            ...$this->formOptions(),
        ]);
    }

    public function update(Request $request, PriceAnalysis $priceAnalysis)
    {
        $validated = $this->validated($request);
        $branch    = Branch::findOrFail($validated['branch_id']);

        // Only re-snapshot the branch's tax/JD-rate defaults when the branch itself actually changes —
        // otherwise this analysis keeps whatever rates it was originally created with.
        $rates = $branch->id === $priceAnalysis->branch_id
            ? ['tax_rate' => $priceAnalysis->tax_rate, 'jd_rate' => $priceAnalysis->jd_rate]
            : ['tax_rate' => $branch->ciat_tax_rate, 'jd_rate' => $branch->ciat_jd_rate];

        $priceAnalysis->update([
            'branch_id' => $branch->id,
            ...$rates,
            'with_tax'  => $request->boolean('with_tax'),
            'notes'     => $validated['notes'] ?? null,
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
        ];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'branch_id'                  => ['required', 'exists:branches,id'],
            'with_tax'                   => ['boolean'],
            'notes'                      => ['nullable', 'string'],
            'items'                      => ['required', 'array', 'min:1'],
            'items.*.ciat_discount_id'   => ['required', 'exists:ciat_discounts,id'],
            'items.*.ciat_model'         => ['required', 'string', 'max:100'],
            'items.*.quantity'           => ['required', 'numeric', 'min:0.001'],
            'items.*.list_price'         => ['required', 'numeric', 'min:0'],
            'items.*.profit'             => ['required', 'numeric'],
            'items.*.shipping'           => ['nullable', 'numeric', 'min:0'],
        ]);
    }

    private function syncItems(PriceAnalysis $analysis, array $items, float $jdRate): void
    {
        $analysis->items()->delete();

        foreach ($items as $item) {
            $discount = CiatDiscount::findOrFail($item['ciat_discount_id']);

            $computed = PriceAnalysisItem::calculate(
                (float) $item['list_price'],
                (float) $discount->discount_percent,
                (float) $item['profit'],
                (float) $item['quantity'],
                $jdRate,
            );

            $analysis->items()->create([
                'ciat_discount_id' => $discount->id,
                'material_id'      => $discount->material_id,
                'ciat_model'       => $item['ciat_model'],
                'quantity'         => $item['quantity'],
                'list_price'       => $item['list_price'],
                'discount_percent' => $discount->discount_percent,
                'profit'           => $item['profit'],
                'shipping'         => $item['shipping'] ?? 0,
                ...$computed,
            ]);
        }
    }
}
