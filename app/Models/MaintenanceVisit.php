<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RuntimeException;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * One technician visit to a customer, grouping one-or-more MaintenanceReport rows filled during
 * it. Lifecycle: open -> submitted_to_customer (technician finishes) -> customer_signed (customer
 * reviews + signs once for the whole visit) -> closed (manager classifies).
 */
class MaintenanceVisit extends Model
{
    use LogsActivity;

    protected $fillable = [
        'customer_id',
        'maintenance_request_id',
        'created_by',
        'check_in_at',
        'check_out_at',
        'status',
        'visit_type_id',
        'classification_note',
        'classified_by',
        'signed_at',
        'signature_path',
        'closed_at',
    ];

    protected $casts = [
        'check_in_at'  => 'datetime',
        'check_out_at' => 'datetime',
        'signed_at'    => 'datetime',
        'closed_at'    => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function maintenanceRequest(): BelongsTo
    {
        return $this->belongsTo(MaintenanceRequest::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(MaintenanceReport::class, 'visit_id');
    }

    public function visitType(): BelongsTo
    {
        return $this->belongsTo(MaintenanceVisitType::class, 'visit_type_id');
    }

    public function classifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'classified_by');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted_to_customer';
    }

    public function isSigned(): bool
    {
        return $this->status === 'customer_signed';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    /** Technician finishes the visit — automatic checkout, moves to the customer's review queue. */
    public function finish(): void
    {
        if ($this->reports()->doesntExist()) {
            throw new RuntimeException('A visit needs at least one report before it can be finished.');
        }

        $this->update([
            'check_out_at' => now(),
            'status'       => 'submitted_to_customer',
        ]);
    }

    /** Customer signs once for the whole visit (applies to every report in it). */
    public function sign(string $signaturePath): void
    {
        $this->update([
            'signed_at'      => now(),
            'signature_path' => $signaturePath,
            'status'         => 'customer_signed',
        ]);
    }

    /** Manager's single "Done" action — classifies and closes in one step. */
    public function classify(MaintenanceVisitType $type, ?string $note, User $manager): void
    {
        $this->update([
            'visit_type_id'        => $type->id,
            'classification_note'  => $type->requires_note ? $note : null,
            'classified_by'        => $manager->id,
            'status'               => 'closed',
            'closed_at'            => now(),
        ]);
    }

    public function getSignatureUrlAttribute(): ?string
    {
        return $this->signature_path ? asset($this->signature_path) : null;
    }

    /** Hours worked, formatted "Xh Ym" — null while the visit is still open. */
    public function getDurationAttribute(): ?string
    {
        if (! $this->check_out_at) {
            return null;
        }

        $minutes = $this->check_in_at->diffInMinutes($this->check_out_at);

        return sprintf('%dh %02dm', intdiv($minutes, 60), $minutes % 60);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
