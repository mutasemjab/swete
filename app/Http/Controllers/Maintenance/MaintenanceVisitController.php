<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\ModuleController;
use App\Models\MaintenanceVisit;
use App\Models\MaintenanceVisitType;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MaintenanceVisitController extends ModuleController
{
    protected string $module = 'maintenance';

    public function index(Request $request)
    {
        $query = MaintenanceVisit::with(['customer', 'technician', 'visitType']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }

        $visits     = $query->latest()->paginate(20)->withQueryString();
        $visitTypes = MaintenanceVisitType::where('status', true)->orderBy('name')->get();

        return $this->moduleView('maintenance.visits.index', compact('visits', 'visitTypes'));
    }

    public function show(MaintenanceVisit $maintenanceVisit)
    {
        $maintenanceVisit->load(['customer', 'technician', 'maintenanceRequest', 'visitType', 'classifiedBy', 'reports.fields', 'reports.materials.material']);
        $visitTypes = MaintenanceVisitType::where('status', true)->orderBy('name')->get();

        return $this->moduleView('maintenance.visits.show', ['visit' => $maintenanceVisit, 'visitTypes' => $visitTypes]);
    }

    public function classify(Request $request, MaintenanceVisit $maintenanceVisit)
    {
        abort_unless($request->user()->is_maintenance_manager, 403);

        $validated = $request->validate([
            'visit_type_id' => ['required', 'exists:maintenance_visit_types,id'],
            'note'          => ['nullable', 'string', 'max:1000'],
        ]);

        $type = MaintenanceVisitType::findOrFail($validated['visit_type_id']);

        if ($type->requires_note && empty($validated['note'])) {
            throw ValidationException::withMessages(['note' => __('maintenance.visit_classification_note_required')]);
        }

        $maintenanceVisit->classify($type, $validated['note'] ?? null, $request->user());

        return redirect()->route('maintenance-visits.index')
            ->with('success', __('maintenance.visit_classified'));
    }
}
