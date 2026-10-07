<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\ModuleController;
use App\Models\MaintenanceContract;
use App\Models\MaintenanceContractScheduledVisit;
use Illuminate\Http\Request;

/** Mirrors ContractPaymentController's shape. */
class ContractScheduledVisitController extends ModuleController
{
    protected string $module = 'maintenance';

    public function index(Request $request)
    {
        $query = MaintenanceContractScheduledVisit::with('contract.customer');

        if ($request->boolean('due')) {
            $query->due();
        }

        $scheduledVisits = $query->orderBy('scheduled_date')->paginate(20)->withQueryString();
        $dueCount        = MaintenanceContractScheduledVisit::due()->count();

        return $this->moduleView('maintenance.contracts.scheduled-visits-index', compact('scheduledVisits', 'dueCount'));
    }

    public function store(Request $request, MaintenanceContract $maintenanceContract)
    {
        $validated = $request->validate([
            'scheduled_date' => ['required', 'date'],
            'type'           => ['required', 'in:' . implode(',', MaintenanceContractScheduledVisit::TYPES)],
            'notes'          => ['nullable', 'string'],
        ]);

        $maintenanceContract->scheduledVisits()->create($validated);

        return back()->with('success', __('maintenance.scheduled_visit_added'));
    }

    public function destroy(MaintenanceContract $maintenanceContract, MaintenanceContractScheduledVisit $scheduledVisit)
    {
        abort_unless($scheduledVisit->contract_id === $maintenanceContract->id, 404);

        $scheduledVisit->delete();

        return back()->with('success', __('maintenance.scheduled_visit_deleted'));
    }
}
