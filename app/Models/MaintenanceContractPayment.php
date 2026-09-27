<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceContractPayment extends Model
{
    protected $fillable = [
        'contract_id',
        'due_date',
        'amount',
        'currency_id',
        'notes',
        'assigned_to',
        'invoice_id',
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount'   => 'decimal:3',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(MaintenanceContract::class, 'contract_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function scopeAssignedTo(Builder $query, int $userId): Builder
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopePendingInvoice(Builder $query): Builder
    {
        return $query->whereNull('invoice_id');
    }
}
