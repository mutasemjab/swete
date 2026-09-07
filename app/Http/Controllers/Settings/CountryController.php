<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\ModuleController;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends ModuleController
{
    protected string $module = 'settings';

    public function index()
    {
        $countries = Country::orderBy('name')->get();
        return $this->moduleView('settings.countries.index', compact('countries'));
    }

    public function create()
    {
        return $this->moduleView('settings.countries.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'name_en' => ['nullable', 'string', 'max:100'],
            'status'  => ['boolean'],
        ]);

        Country::create([
            ...$validated,
            'status' => $request->boolean('status', true),
        ]);

        return redirect()->route('settings.countries.index')
            ->with('success', __('settings.country_added'));
    }

    public function edit(Country $country)
    {
        return $this->moduleView('settings.countries.edit', compact('country'));
    }

    public function update(Request $request, Country $country)
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'name_en' => ['nullable', 'string', 'max:100'],
            'status'  => ['boolean'],
        ]);

        $country->update([
            ...$validated,
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('settings.countries.index')
            ->with('success', __('settings.country_updated'));
    }

    public function destroy(Country $country)
    {
        $country->delete();

        return redirect()->route('settings.countries.index')
            ->with('success', __('settings.country_deleted'));
    }
}
