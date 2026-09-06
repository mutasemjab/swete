<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Invoice extends Model
{
    use LogsActivity;

    protected $fillable = [
        'invoice_type_id',
        'party_type',
        'party_id',
        'number',
        'date',
        'due_date',
        'currency_id',
        'status',
        'subtotal',
        'tax_total',
        'total',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'date'      => 'date',
        'due_date'  => 'date',
        'subtotal'  => 'decimal:3',
        'tax_total' => 'decimal:3',
        'total'     => 'decimal:3',
    ];

    public function invoiceType(): BelongsTo
    {
        return $this->belongsTo(InvoiceType::class);
    }

    /**
     * `party_type` ('customer'|'supplier') says which table `party_id` points into —
     * customers and suppliers are separate tables, not a shared/polymorphic one, so
     * this picks the right concrete relation rather than using a real morphTo.
     */
    public function party(): BelongsTo
    {
        return $this->party_type === 'supplier'
            ? $this->belongsTo(Supplier::class, 'party_id')
            : $this->belongsTo(Customer::class, 'party_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public static function nextNumber(InvoiceType $type): string
    {
        $prefix = strtoupper(substr($type->code, 0, 3));
        $year   = now()->format('Y');
        $count  = static::where('invoice_type_id', $type->id)->whereYear('created_at', $year)->count() + 1;

        return sprintf('%s-%s-%05d', $prefix, $year, $count);
    }

    /** Recompute subtotal/total from the current line items. */
    public function recalculateTotals(): void
    {
        $subtotal = $this->items->sum(fn ($item) => $item->quantity * $item->unit_price);

        $this->update([
            'subtotal' => $subtotal,
            'total'    => $subtotal + $this->tax_total,
        ]);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
