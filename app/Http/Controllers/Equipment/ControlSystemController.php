<?php

namespace App\Http\Controllers\Equipment;

use App\Http\Controllers\ModuleController;
use App\Models\ControlSystem;
use Illuminate\Http\Request;

class ControlSystemController extends ModuleController
{
    protected string $module = 'equipment';

    public function index()
    {
        $controlSystems = ControlSystem::orderBy('name')->get();

        return $this->moduleView('equipment.control-systems.index', compact('controlSystems'));
    }

    public function create()
    {
        return $this->moduleView('equipment.control-systems.create', ['controlSystem' => null]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        ControlSystem::create([
            ...$validated,
            'status' => $request->boolean('status', true),
        ]);

        return redirect()->route('control-systems.index')
            ->with('success', __('equipment.control_system_added'));
    }

    public function edit(ControlSystem $controlSystem)
    {
        return $this->moduleView('equipment.control-systems.edit', ['controlSystem' => $controlSystem]);
    }

    public function update(Request $request, ControlSystem $controlSystem)
    {
        $validated = $this->validated($request);

        $controlSystem->update([
            ...$validated,
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('control-systems.index')
            ->with('success', __('equipment.control_system_updated'));
    }

    public function destroy(ControlSystem $controlSystem)
    {
        if ($controlSystem->models()->exists() || $controlSystem->alarmCodes()->exists()) {
            return back()->with('error', __('equipment.control_system_in_use'));
        }

        $controlSystem->delete();

        return redirect()->route('control-systems.index')
            ->with('success', __('equipment.control_system_deleted'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'    => ['required', 'string', 'max:150', 'unique:control_systems,name,'.$request->route('control_system')?->id],
            'name_en' => ['nullable', 'string', 'max:150'],
        ]);
    }
}
