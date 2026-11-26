<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\ModuleController;
use App\Models\MaintenanceContract;
use App\Models\MaintenanceContractSchedule;
use App\Models\User;
use App\Support\ContractVisitSync;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

        DB::transaction(function () use ($maintenanceContract, $validated) {
            MaintenanceContract::whereKey($maintenanceContract->id)->lockForUpdate()->firstOrFail();
            $previous = MaintenanceContractSchedule::where('contract_id', $maintenanceContract->id)
                ->where('scope', $validated['scope'])->where('is_active', true);
            if ($validated['scope'] === 'unit') {
                $previous->where('equipment_unit_id', $validated['equipment_unit_id']);
            }
            $previous->update(['is_active' => false]);
            $schedule = MaintenanceContractSchedule::create([...$validated, 'contract_id' => $maintenanceContract->id, 'is_active' => true]);
            $this->captureCoverage($schedule, $maintenanceContract);
            ContractVisitSync::sync($maintenanceContract);
        });

        return redirect()->route('maintenance-contracts.show', $maintenanceContract)
            ->with('success', __('maintenance.schedule_added'));
    }

    public function edit(MaintenanceContract $maintenanceContract, MaintenanceContractSchedule $schedule)
    {
        abort_unless($schedule->contract_id === $maintenanceContract->id, 404);
        return $this->moduleView('maintenance.contracts.schedules.edit', [
            'contract' => $maintenanceContract,
            'schedule' => $schedule,
        ] + $this->pickers($maintenanceContract));
    }

    public function update(Request $request, MaintenanceContract $maintenanceContract, MaintenanceContractSchedule $schedule)
    {
        abort_unless($schedule->contract_id === $maintenanceContract->id, 404);
        $validated = $this->validated($request, $maintenanceContract, $schedule);

        DB::transaction(function () use ($schedule, $maintenanceContract, $validated) {
            MaintenanceContract::whereKey($maintenanceContract->id)->lockForUpdate()->firstOrFail();
            $schedule->refresh();
            $previouslyIncludedNewUnits = $schedule->include_new_units;
            $previousScope = $schedule->scope;
            $schedule->update($validated);
            if (($previouslyIncludedNewUnits || $previousScope !== 'general') && ! $schedule->include_new_units) {
                $this->captureCoverage($schedule, $maintenanceContract);
            }
            ContractVisitSync::sync($maintenanceContract);
        });

        return redirect()->route('maintenance-contracts.show', $maintenanceContract)
            ->with('success', __('maintenance.schedule_updated'));
    }

    public function destroy(MaintenanceContract $maintenanceContract, MaintenanceContractSchedule $schedule)
    {
        abort_unless($schedule->contract_id === $maintenanceContract->id, 404);
        // Deactivate, never hard-delete — generated visits still reference it; re-sync clears
        // its untouched future visits and "history was kept" for anything already notified.
        DB::transaction(function () use ($schedule, $maintenanceContract) {
            MaintenanceContract::whereKey($maintenanceContract->id)->lockForUpdate()->firstOrFail();
            $schedule->update(['is_active' => false]);
            ContractVisitSync::sync($maintenanceContract);
        });

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

    private function captureCoverage(MaintenanceContractSchedule $schedule, MaintenanceContract $contract): void
    {
        if ($schedule->scope === 'general' && ! $schedule->include_new_units && Schema::hasTable('maintenance_schedule_units')) {
            $schedule->coveredUnits()->sync($contract->activeUnits()->pluck('equipment_unit_id'));
        }
    }

    private function validated(Request $request, MaintenanceContract $contract, ?MaintenanceContractSchedule $schedule = null): array
    {
        $rules = [
            'scope'              => ['required', 'in:'.implode(',', MaintenanceContractSchedule::SCOPES)],
            'equipment_unit_id'  => ['required_if:scope,unit', 'nullable', Rule::in($contract->activeUnits()->pluck('equipment_unit_id')->all())],
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
        validator(['dates' => $validated['manual_dates']], ['dates.*' => ['date_format:Y-m-d']])->validate();

        $validated['include_new_units'] = $request->boolean('include_new_units', true);

        if ($validated['scope'] === 'general') {
            $validated['equipment_unit_id'] = null;
        }

        return $validated;
    }
}
