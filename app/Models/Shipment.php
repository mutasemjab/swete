<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Shipment extends Model
{
    use LogsActivity;

    public const STATUSES = ['pending', 'in_transit', 'arrived', 'delivered'];

    public const STATUS_COLORS = [
        'pending'    => 'amber',
        'in_transit' => 'indigo',
        'arrived'    => 'cyan',
        'delivered'  => 'emerald',
    ];

    public const TRANSPORT_MODES = ['sea', 'land', 'air'];

    public const SEA_SERVICE_TYPES = ['lcl', '20ft', '40ft', '40hc'];

    public const AIR_SERVICE_TYPES = ['express', 'air_freight'];

    /** Placeholder set — the user asked to start with something reasonable and will refine it later. */
    public const INCOTERMS = ['exw', 'fca', 'fas', 'fob', 'cfr', 'cif', 'cpt', 'cip', 'dap', 'dpu', 'ddp'];

    protected $fillable = [
        'number',
        'shipping_company_id',
        'status',
        'transport_mode',
        'sea_service_type',
        'air_service_type',
        'incoterm',
        'price',
        'currency_id',
        'is_hazardous',
        'shipping_line',
        'bill_of_lading_number',
        'container_number',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'price'        => 'decimal:3',
        'is_hazardous' => 'boolean',
    ];

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'slate';
    }

    public function shippingCompany(): BelongsTo
    {
        return $this->belongsTo(ShippingCompany::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function purchaseRequests(): BelongsToMany
    {
        return $this->belongsToMany(PurchaseRequest::class);
    }

    public static function nextNumber(): string
    {
        $year  = now()->format('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;

        return sprintf('SHP-%s-%05d', $year, $count);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
