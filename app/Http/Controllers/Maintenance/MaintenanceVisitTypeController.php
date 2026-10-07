<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\ModuleController;
use App\Models\MaintenanceVisitType;
use Illuminate\Http\Request;

/** Dynamic/manageable classification list for closed visits — "add anything in the future" without code changes. */
class MaintenanceVisitTypeController extends ModuleController
{
    protected string $module = 'maintenance';

    public function index()
    {
        $visitTypes = MaintenanceVisitType::orderBy('name')->get();
        return $this->moduleView('maintenance.visit-types.index', compact('visitTypes'));
    }

    public function create()
    {
        return $this->moduleView('maintenance.visit-types.create', ['visitType' => null]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        MaintenanceVisitType::create([
            ...$validated,
            'requires_note' => $request->boolean('requires_note'),
            'status'        => $request->boolean('status', true),
        ]);

        return redirect()->route('maintenance-visit-types.index')
            ->with('success', __('maintenance.visit_type_added'));
    }

    public function edit(MaintenanceVisitType $maintenanceVisitType)
    {
        return $this->moduleView('maintenance.visit-types.edit', ['visitType' => $maintenanceVisitType]);
    }

    public function update(Request $request, MaintenanceVisitType $maintenanceVisitType)
    {
        $validated = $this->validated($request);

        $maintenanceVisitType->update([
            ...$validated,
            'requires_note' => $request->boolean('requires_note'),
            'status'        => $request->boolean('status'),
        ]);

        return redirect()->route('maintenance-visit-types.index')
            ->with('success', __('maintenance.visit_type_updated'));
    }

    public function destroy(MaintenanceVisitType $maintenanceVisitType)
    {
        if ($maintenanceVisitType->visits()->exists()) {
            return back()->with('error', __('maintenance.visit_type_in_use'));
        }

        $maintenanceVisitType->delete();

        return redirect()->route('maintenance-visit-types.index')
            ->with('success', __('maintenance.visit_type_deleted'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'name_en' => ['nullable', 'string', 'max:100'],
        ]);
    }
}
