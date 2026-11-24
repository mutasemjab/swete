<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * The recurrence rule a contract's visits are generated from. "general" covers every unit on
 * the contract (unless opted out via include_new_units=false); "unit" overrides the general
 * rule for exactly one unit — see App\Support\ContractVisitSync::scheduleForUnit().
 */
class MaintenanceContractSchedule extends Model
{
    public const SCOPES = ['general', 'unit'];
    public const MODES = ['interval', 'manual'];
    public const INTERVAL_UNITS = ['day', 'week', 'month'];

    protected $fillable = [
        'contract_id',
        'scope',
        'equipment_unit_id',
        'mode',
        'interval_value',
        'interval_unit',
        'manual_dates',
        'start_date',
        'end_date',
        'include_new_units',
        'engineer_id',
        'technician_id',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'manual_dates'       => 'array',
        'start_date'         => 'date',
        'end_date'           => 'date',
        'include_new_units'  => 'boolean',
        'is_active'          => 'boolean',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(MaintenanceContract::class, 'contract_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(EquipmentUnit::class, 'equipment_unit_id');
    }

    public function engineer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'engineer_id');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    /**
     * Every date this schedule produces, capped to the contract's own end date and never
     * before the contract's start date (the contract may have moved after this was saved).
     */
    public function plannedDates(): array
    {
        $contract = $this->contract;
        $windowStart = $contract->signed_date->copy()->max($this->start_date);
        $windowEnd = $this->end_date ? $this->end_date->copy()->min($contract->expiry_date) : $contract->expiry_date;

        if ($this->mode === 'manual') {
            return collect($this->manual_dates ?? [])
                ->map(fn ($d) => Carbon::parse($d))
                ->filter(fn (Carbon $d) => $d->between($windowStart, $windowEnd))
                ->sort()
                ->map(fn (Carbon $d) => $d->toDateString())
                ->values()
                ->all();
        }

        $dates = [];
        $cursor = $this->start_date->copy();
        $step = match ($this->interval_unit) {
            'day'   => fn (Carbon $d) => $d->addDays($this->interval_value),
            'week'  => fn (Carbon $d) => $d->addWeeks($this->interval_value),
            'month' => fn (Carbon $d) => $d->addMonthsNoOverflow($this->interval_value),
        };

        while ($cursor->lessThanOrEqualTo($windowEnd)) {
            if ($cursor->greaterThanOrEqualTo($windowStart)) {
                $dates[] = $cursor->toDateString();
            }
            $cursor = $step($cursor->copy());
        }

        return $dates;
    }
}
