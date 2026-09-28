<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** One material/quantity actually used during a maintenance visit — feeds the stock-issue voucher once approved. */
class MaintenanceReportMaterial extends Model
{
    protected $fillable = [
        'report_id',
        'material_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(MaintenanceReport::class, 'report_id');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}
