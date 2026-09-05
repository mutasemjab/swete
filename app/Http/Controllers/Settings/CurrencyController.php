<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\ModuleController;
use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends ModuleController
{
    protected string $module = 'settings';

    public function index()
    {
        $currencies = Currency::orderByDesc('is_default')->orderBy('name')->get();
        return $this->moduleView('settings.currencies.index', compact('currencies'));
    }

    public function create()
    {
        return $this->moduleView('settings.currencies.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'name_en'       => ['nullable', 'string', 'max:100'],
            'code'          => ['required', 'string', 'size:3', 'unique:currencies,code'],
            'symbol'        => ['required', 'string', 'max:10'],
            'exchange_rate' => ['required', 'numeric', 'min:0'],
            'is_default'    => ['boolean'],
            'status'        => ['boolean'],
        ]);

        if ($request->boolean('is_default')) {
            Currency::where('is_default', true)->update(['is_default' => false]);
        }

        Currency::create([
            ...$validated,
            'code'       => strtoupper($validated['code']),
            'is_default' => $request->boolean('is_default'),
            'status'     => $request->boolean('status', true),
        ]);

        return redirect()->route('settings.currencies.index')
            ->with('success', __('settings.currency_added'));
    }

    public function edit(Currency $currency)
    {
        return $this->moduleView('settings.currencies.edit', compact('currency'));
    }

    public function update(Request $request, Currency $currency)
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'name_en'       => ['nullable', 'string', 'max:100'],
            'code'          => ['required', 'string', 'size:3', "unique:currencies,code,{$currency->id}"],
            'symbol'        => ['required', 'string', 'max:10'],
            'exchange_rate' => ['required', 'numeric', 'min:0'],
            'is_default'    => ['boolean'],
            'status'        => ['boolean'],
        ]);

        if ($request->boolean('is_default') && ! $currency->is_default) {
            Currency::where('is_default', true)->update(['is_default' => false]);
        }

        $currency->update([
            ...$validated,
            'code'       => strtoupper($validated['code']),
            'is_default' => $request->boolean('is_default'),
            'status'     => $request->boolean('status'),
        ]);

        return redirect()->route('settings.currencies.index')
            ->with('success', __('settings.currency_updated'));
    }

    public function destroy(Currency $currency)
    {
        if ($currency->is_default) {
            return back()->with('error', __('settings.currency_deleted'));
        }

        $currency->delete();

        return redirect()->route('settings.currencies.index')
            ->with('success', __('settings.currency_deleted'));
    }
}
