<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** One approver's decision row, snapshotted from maintenance_report_material_approvers when a report's materials-used lines are saved. */
class MaintenanceReportMaterialApproval extends Model
{
    protected $fillable = [
        'report_id',
        'user_id',
        'decision',
        'decided_at',
        'note',
    ];

    protected $casts = [
        'decided_at' => 'datetime',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(MaintenanceReport::class, 'report_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
