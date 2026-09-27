<?php

namespace App\Http\Controllers\Tenders;

use App\Http\Controllers\ModuleController;
use App\Models\CiatDiscount;
use App\Models\Material;
use Illuminate\Http\Request;

/** Dynamic/manageable list of CIAT product discounts — looked up when building a Price Analysis line. */
class CiatDiscountController extends ModuleController
{
    protected string $module = 'tenders';

    public function index()
    {
        $discounts = CiatDiscount::with('material')->get()
            ->sortBy(fn ($discount) => $discount->material?->localized_name)->values();

        return $this->moduleView('tenders.ciat-discounts.index', compact('discounts'));
    }

    public function create()
    {
        return $this->moduleView('tenders.ciat-discounts.create', [
            'discount'  => null,
            'materials' => Material::where('status', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        CiatDiscount::create([
            ...$validated,
            'status'     => $request->boolean('status', true),
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('ciat-discounts.index')
            ->with('success', __('tenders.ciat_discount_added'));
    }

    public function edit(CiatDiscount $ciatDiscount)
    {
        return $this->moduleView('tenders.ciat-discounts.edit', [
            'discount'  => $ciatDiscount,
            'materials' => Material::where('status', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, CiatDiscount $ciatDiscount)
    {
        $validated = $this->validated($request);

        $ciatDiscount->update([
            ...$validated,
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('ciat-discounts.index')
            ->with('success', __('tenders.ciat_discount_updated'));
    }

    public function destroy(CiatDiscount $ciatDiscount)
    {
        if ($ciatDiscount->items()->exists()) {
            return back()->with('error', __('tenders.ciat_discount_in_use'));
        }

        $ciatDiscount->delete();

        return redirect()->route('ciat-discounts.index')
            ->with('success', __('tenders.ciat_discount_deleted'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'material_id'      => ['required', 'exists:materials,id'],
            'discount_percent' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);
    }
}
