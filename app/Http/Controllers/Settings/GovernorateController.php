<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\ModuleController;
use App\Models\Governorate;
use Illuminate\Http\Request;

class GovernorateController extends ModuleController
{
    protected string $module = 'settings';

    public function index()
    {
        $governorates = Governorate::orderBy('name')->get();
        return $this->moduleView('settings.governorates.index', compact('governorates'));
    }

    public function create()
    {
        return $this->moduleView('settings.governorates.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        Governorate::create([
            ...$validated,
            'status' => $request->boolean('status', true),
        ]);

        return redirect()->route('settings.governorates.index')
            ->with('success', __('settings.governorate_added'));
    }

    public function edit(Governorate $governorate)
    {
        return $this->moduleView('settings.governorates.edit', compact('governorate'));
    }

    public function update(Request $request, Governorate $governorate)
    {
        $validated = $this->validated($request);

        $governorate->update([
            ...$validated,
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('settings.governorates.index')
            ->with('success', __('settings.governorate_updated'));
    }

    public function destroy(Governorate $governorate)
    {
        if ($governorate->isInUse()) {
            return back()->with('error', __('settings.governorate_in_use'));
        }

        $governorate->delete();

        return redirect()->route('settings.governorates.index')
            ->with('success', __('settings.governorate_deleted'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'name_en' => ['nullable', 'string', 'max:100'],
            'status'  => ['boolean'],
        ]);
    }
}
