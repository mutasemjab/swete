<?php

namespace App\Models;

use App\Models\Concerns\FormatsAddressLines;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PurchaseRequest extends Model
{
    use LogsActivity, FormatsAddressLines;

    protected $fillable = [
        'number',
        'project_id',
        'service_call_id',
        'date',
        'supplier_id',
        'branch_id',
        'shipping_address_line1',
        'shipping_address_line1_en',
        'shipping_po_box',
        'shipping_postal_code',
        'shipping_city',
        'shipping_city_en',
        'shipping_country',
        'shipping_country_en',
        'location_scope',
        'governorate',
        'country_id',
        'currency_id',
        'subtotal',
        'total',
        'notes',
        'status',
        'so_number',
        'ready_date',
        'created_by',
    ];

    protected $casts = [
        'date'       => 'date',
        'ready_date' => 'date',
        'subtotal'   => 'decimal:3',
        'total'      => 'decimal:3',
    ];

    public const STATUSES = ['pending_approval', 'approved', 'rejected', 'sent', 'manufacturing', 'awaiting_price_quotes', 'shipped'];

    public const STATUS_COLORS = [
        'pending_approval'      => 'amber',
        'approved'              => 'emerald',
        'rejected'              => 'rose',
        'sent'                  => 'indigo',
        'manufacturing'         => 'cyan',
        'awaiting_price_quotes' => 'violet',
        'shipped'               => 'teal',
    ];

    /** Preset labels the "Additional Notes" section starts with on the create form — removable, not a fixed enum. */
    public const DEFAULT_NOTE_LABELS = [
        'Incoterm',
        'Payment Term',
        'Attached Files',
        'Language of Documentation',
        'All Documents Shall be Sent To',
        'Shipment Desired Date',
    ];

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'slate';
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function serviceCall(): BelongsTo
    {
        return $this->belongsTo(ServiceCall::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
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
        return $this->hasMany(PurchaseRequestItem::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(PurchaseRequestApproval::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(PurchaseRequestAttachment::class);
    }

    public function shippingRequests(): HasMany
    {
        return $this->hasMany(PurchaseRequestShippingRequest::class);
    }

    public function shipments(): BelongsToMany
    {
        return $this->belongsToMany(Shipment::class);
    }

    public function additionalNotes(): HasMany
    {
        return $this->hasMany(PurchaseRequestNote::class);
    }

    /** Locked once shipped — the request is considered finalized at that point. */
    public function isEditable(): bool
    {
        return $this->status !== 'shipped';
    }

    /** Stacked, locale-aware request address — derived live from the branch, not duplicated here. */
    public function getRequestAddressLinesAttribute(): array
    {
        return $this->branch?->localized_address_lines ?? [];
    }

    /** Stacked, locale-aware shipping address — where the goods actually go (may differ from the branch). */
    public function getShippingAddressLinesAttribute(): array
    {
        return $this->addressLines(
            $this->shipping_address_line1, $this->shipping_address_line1_en,
            $this->shipping_po_box, $this->shipping_postal_code,
            $this->shipping_city, $this->shipping_city_en,
            $this->shipping_country, $this->shipping_country_en,
        );
    }

    public function getLocalizedGovernorateAttribute(): ?string
    {
        if (! $this->governorate || ! isset(Tender::JORDAN_GOVERNORATES[$this->governorate])) {
            return null;
        }

        return Tender::JORDAN_GOVERNORATES[$this->governorate][app()->isLocale('en') ? 'en' : 'ar'];
    }

    public static function nextNumber(): string
    {
        $year  = now()->format('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;

        return sprintf('PR-%s-%05d', $year, $count);
    }

    public function recalculateTotals(): void
    {
        // Query fresh rather than trust a possibly stale cached `items` relation
        // (e.g. right after syncing a new set of items on an already-loaded model).
        $subtotal = $this->items()->get()->sum(fn ($item) => $item->quantity * $item->unit_price);

        $this->update([
            'subtotal' => $subtotal,
            'total'    => $subtotal,
        ]);
    }

    /** Snapshot the current PurchaseRequestApprover pool into per-PR approval rows. Called once, right after creation. */
    public function seedApprovals(): void
    {
        foreach (PurchaseRequestApprover::pluck('user_id') as $userId) {
            $this->approvals()->create(['user_id' => $userId]);
        }
    }

    /** Record one approver's decision, then re-evaluate the PR's overall status if everyone has now responded. */
    public function recordDecision(User $user, string $decision, ?string $note = null): void
    {
        $approval = $this->approvals()->where('user_id', $user->id)->where('decision', 'pending')->first();

        if (! $approval) {
            return;
        }

        $approval->update(['decision' => $decision, 'decided_at' => now(), 'note' => $note]);

        $this->resolveApprovalStatus();
    }

    private function resolveApprovalStatus(): void
    {
        if ($this->approvals()->where('decision', 'pending')->exists()) {
            return;
        }

        $this->update([
            'status' => $this->approvals()->where('decision', 'rejected')->exists() ? 'rejected' : 'approved',
        ]);
    }

    /** No approvers were configured when this PR was created, so no one can ever approve it through the normal flow. */
    public function canApproveManually(): bool
    {
        return $this->status === 'pending_approval' && $this->approvals()->doesntExist();
    }

    public function markApprovedManually(): bool
    {
        if (! $this->canApproveManually()) {
            return false;
        }

        $this->update(['status' => 'approved']);

        return true;
    }

    public function markSent(): bool
    {
        if ($this->status !== 'approved') {
            return false;
        }

        $this->update(['status' => 'sent']);

        return true;
    }

    /** Setting both SO number and ready date advances a sent PR into manufacturing. */
    public function updateManufacturingInfo(string $soNumber, string $readyDate): void
    {
        $this->update([
            'so_number'  => $soNumber,
            'ready_date' => $readyDate,
        ]);

        if ($this->status === 'sent') {
            $this->update(['status' => 'manufacturing']);
        }
    }

    public function markAwaitingPriceQuotes(): void
    {
        $this->update(['status' => 'awaiting_price_quotes']);
    }

    public function markShipped(): void
    {
        $this->update(['status' => 'shipped']);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
