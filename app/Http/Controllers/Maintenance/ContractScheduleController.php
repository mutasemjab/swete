<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\ModuleController;
use App\Models\MaintenanceContract;
use App\Models\MaintenanceContractSchedule;
use App\Models\User;
use App\Support\BackNavigation;
use App\Support\ContractVisitSync;
use Illuminate\Http\Request;

class ContractScheduleController extends ModuleController
{
    protected string $module = 'maintenance';

    public function create(MaintenanceContract $maintenanceContract)
    {
        return $this->moduleView('maintenance.contracts.schedules.create', [
            'contract' => $maintenanceContract,
            'schedule' => null,
        ] + $this->pickers($maintenanceContract));
    }

    public function store(Request $request, MaintenanceContract $maintenanceContract)
    {
        $validated = $this->validated($request, $maintenanceContract);

        if ($validated['scope'] === 'general') {
            MaintenanceContractSchedule::where('contract_id', $maintenanceContract->id)
                ->where('scope', 'general')->where('is_active', true)
                ->update(['is_active' => false]);
        } else {
            MaintenanceContractSchedule::where('contract_id', $maintenanceContract->id)
                ->where('scope', 'unit')->where('equipment_unit_id', $validated['equipment_unit_id'])
                ->where('is_active', true)->update(['is_active' => false]);
        }

        MaintenanceContractSchedule::create([...$validated, 'contract_id' => $maintenanceContract->id, 'is_active' => true]);

        ContractVisitSync::sync($maintenanceContract);

        return redirect()->route('maintenance-contracts.show', $maintenanceContract)
            ->with('success', __('maintenance.schedule_added'));
    }

    public function edit(MaintenanceContract $maintenanceContract, MaintenanceContractSchedule $schedule)
    {
        return $this->moduleView('maintenance.contracts.schedules.edit', [
            'contract' => $maintenanceContract,
            'schedule' => $schedule,
        ] + $this->pickers($maintenanceContract));
    }

    public function update(Request $request, MaintenanceContract $maintenanceContract, MaintenanceContractSchedule $schedule)
    {
        $validated = $this->validated($request, $maintenanceContract, $schedule);

        $schedule->update($validated);

        ContractVisitSync::sync($maintenanceContract);

        return redirect()->route('maintenance-contracts.show', $maintenanceContract)
            ->with('success', __('maintenance.schedule_updated'));
    }

    public function destroy(MaintenanceContract $maintenanceContract, MaintenanceContractSchedule $schedule)
    {
        // Deactivate, never hard-delete — generated visits still reference it; re-sync clears
        // its untouched future visits and "history was kept" for anything already notified.
        $schedule->update(['is_active' => false]);

        ContractVisitSync::sync($maintenanceContract);

        return redirect()->route('maintenance-contracts.show', $maintenanceContract)
            ->with('success', __('maintenance.schedule_deleted'));
    }

    private function pickers(MaintenanceContract $contract): array
    {
        return [
            'units' => $contract->activeUnits()->get()->pluck('unit'),
            'users' => User::where('status', true)->orderBy('name')->get(),
        ];
    }

    private function validated(Request $request, MaintenanceContract $contract, ?MaintenanceContractSchedule $schedule = null): array
    {
        $rules = [
            'scope'              => ['required', 'in:'.implode(',', MaintenanceContractSchedule::SCOPES)],
            'equipment_unit_id'  => ['required_if:scope,unit', 'nullable', 'exists:equipment_units,id'],
            'mode'               => ['required', 'in:'.implode(',', MaintenanceContractSchedule::MODES)],
            'interval_value'     => ['required_if:mode,interval', 'nullable', 'integer', 'min:1', 'max:60'],
            'interval_unit'      => ['required_if:mode,interval', 'nullable', 'in:'.implode(',', MaintenanceContractSchedule::INTERVAL_UNITS)],
            'manual_dates_text'  => ['required_if:mode,manual', 'nullable', 'string'],
            'start_date'         => ['required', 'date'],
            'end_date'           => ['nullable', 'date', 'after_or_equal:start_date'],
            'include_new_units'  => ['nullable', 'boolean'],
            'engineer_id'        => ['nullable', 'exists:users,id'],
            'technician_id'      => ['nullable', 'exists:users,id'],
            'notes'              => ['nullable', 'string'],
        ];

        $validated = $request->validate($rules);

        $validated['manual_dates'] = $validated['mode'] === 'manual'
            ? collect(preg_split('/[\r\n,]+/', $validated['manual_dates_text'] ?? ''))
                ->map(fn ($d) => trim($d))->filter()->values()->all()
            : null;
        unset($validated['manual_dates_text']);

        $validated['include_new_units'] = $request->boolean('include_new_units', true);

        if ($validated['scope'] === 'general') {
            $validated['equipment_unit_id'] = null;
        }

        return $validated;
    }
}
