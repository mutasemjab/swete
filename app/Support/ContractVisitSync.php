<?php

namespace App\Support;

use App\Models\MaintenanceContract;
use App\Models\MaintenanceContractScheduledVisit;
use App\Models\MaintenanceContractSchedule;
use Illuminate\Support\Facades\DB;

/**
 * Turns a contract's schedules into real MaintenanceContractScheduledVisit rows — idempotent,
 * and never touches a row that's already been notified about or doesn't belong to a schedule.
 * Mirrors the HVAC source app's own sync_contract_visits: individual schedules beat the general
 * one, only untouched future rows are ever replaced, and past dates are never (re)generated.
 */
class ContractVisitSync
{
    /** The schedule that wins for one unit — an active unit-schedule beats the active general one. */
    public static function scheduleForUnit(MaintenanceContract $contract, int $unitId, $schedules = null): ?MaintenanceContractSchedule
    {
        $schedules ??= $contract->schedules()->where('is_active', true)->get();

        $unitSchedule = $schedules->first(fn (MaintenanceContractSchedule $s) => $s->scope === 'unit' && $s->equipment_unit_id === $unitId);
        if ($unitSchedule) {
            return $unitSchedule;
        }

        $general = $schedules->first(fn (MaintenanceContractSchedule $s) => $s->scope === 'general');
        if (! $general) {
            return null;
        }

        if ($general->include_new_units) {
            return $general;
        }

        // include_new_units=false means only units explicitly covered by this contract when the
        // schedule was last saved are included — approximated here as "every currently active
        // contract unit", since there's no separate opt-in list to track for a V1 scope.
        return $general;
    }

    public static function sync(MaintenanceContract $contract): void
    {
        DB::transaction(function () use ($contract) {
            $contract = MaintenanceContract::whereKey($contract->id)->lockForUpdate()->first();
            $today = now()->toDateString();

            $desired = []; // "{unitId}|{date}" => [unitId, date, schedule]

            if ($contract->inForce()) {
                $schedules = $contract->schedules()->where('is_active', true)->get();
                $unitIds = $contract->activeUnits()->pluck('equipment_unit_id');

                foreach ($unitIds as $unitId) {
                    $schedule = self::scheduleForUnit($contract, $unitId, $schedules);
                    if (! $schedule) {
                        continue;
                    }
                    foreach ($schedule->plannedDates() as $date) {
                        if ($date < $today) {
                            continue;
                        }
                        $desired["{$unitId}|{$date}"] = [$unitId, $date, $schedule];
                    }
                }
            }

            $future = MaintenanceContractScheduledVisit::where('contract_id', $contract->id)
                ->whereNotNull('schedule_id')
                ->where('scheduled_date', '>=', $today)
                ->get();

            foreach ($future as $visit) {
                $key = "{$visit->equipment_unit_id}|{$visit->scheduled_date->toDateString()}";

                if (! isset($desired[$key])) {
                    if ($visit->isUntouched()) {
                        $visit->delete();
                    }
                    continue;
                }

                [, , $schedule] = $desired[$key];
                if ($visit->schedule_id !== $schedule->id && $visit->isUntouched()) {
                    $visit->update(['schedule_id' => $schedule->id, 'source' => $schedule->scope]);
                }

                unset($desired[$key]); // already satisfied — don't create a duplicate below
            }

            foreach ($desired as [$unitId, $date, $schedule]) {
                MaintenanceContractScheduledVisit::create([
                    'contract_id'       => $contract->id,
                    'equipment_unit_id' => $unitId,
                    'schedule_id'       => $schedule->id,
                    'source'            => $schedule->scope,
                    'scheduled_date'    => $date,
                    'type'              => 'periodic',
                ]);
            }
        });
    }

    /** Called on suspend/terminate — re-syncs (clears untouched future generated rows), nothing else to do since a plain scheduled-visit row has no "cancel" state of its own. */
    public static function close(MaintenanceContract $contract): void
    {
        self::sync($contract);
    }
}
