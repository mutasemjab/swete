<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\ModuleController;
use App\Models\QuoteDiscountLimit;
use Illuminate\Http\Request;

class QuoteDiscountLimitController extends ModuleController
{
    protected string $module = 'settings';

    public function index()
    {
        $limit = QuoteDiscountLimit::current();

        return $this->moduleView('settings.quote-discount-limit.index', compact('limit'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'max_discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'max_discount_amount'  => ['nullable', 'numeric', 'min:0'],
        ]);

        QuoteDiscountLimit::query()->firstOrNew()->fill([
            ...$validated,
            'updated_by' => $request->user()->id,
        ])->save();

        return redirect()->route('settings.quote-discount-limit.index')
            ->with('success', __('settings.quote_discount_limit_saved'));
    }
}
