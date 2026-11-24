<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** One equipment unit linked to a contract — soft-removed (removed_at set) so history survives unlinking. */
class MaintenanceContractUnit extends Model
{
    protected $fillable = [
        'contract_id',
        'equipment_unit_id',
        'added_at',
        'removed_at',
    ];

    protected $casts = [
        'added_at'   => 'datetime',
        'removed_at' => 'datetime',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(MaintenanceContract::class, 'contract_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(EquipmentUnit::class, 'equipment_unit_id');
    }
}
