<?php

namespace App\Models;

use App\Models\Concerns\HasApprovals;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class MaterialRequest extends Model
{
    use HasApprovals, LogsActivity;

    protected $fillable = [
        'number',
        'warehouse_id',
        'requested_by',
        'needed_by_date',
        'reason',
        'status',
        'fulfilled_stock_voucher_id',
    ];

    protected $casts = [
        'needed_by_date' => 'date',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(MaterialRequestItem::class);
    }

    public function fulfilledVoucher(): BelongsTo
    {
        return $this->belongsTo(StockVoucher::class, 'fulfilled_stock_voucher_id');
    }

    public static function nextNumber(): string
    {
        $year  = now()->format('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;

        return sprintf('MR-%s-%05d', $year, $count);
    }

    public function approvalLabel(): string
    {
        return __('warehouse.material_request') . ' #' . $this->number;
    }

    /** Called by ApprovalService right after the target approver approves. */
    public function onApproved(): void
    {
        $this->update(['status' => 'approved']);
    }

    /** Called by ApprovalService right after the target approver rejects. */
    public function onRejected(): void
    {
        $this->update(['status' => 'rejected']);
    }

    /**
     * Turn an approved request into a posted issue voucher from its warehouse,
     * linking it back so the request can't be fulfilled twice.
     */
    public function fulfill(User $user): StockVoucher
    {
        if ($this->status !== 'approved') {
            throw new \RuntimeException('warehouse.request_not_approved');
        }

        return DB::transaction(function () use ($user) {
            $voucher = StockVoucher::create([
                'type'         => 'issue',
                'number'       => StockVoucher::nextNumber('issue'),
                'warehouse_id' => $this->warehouse_id,
                'date'         => now()->toDateString(),
                'reference_no' => $this->number,
                'notes'        => $this->reason,
                'status'       => 'draft',
                'created_by'   => $user->id,
            ]);

            foreach ($this->items as $item) {
                $voucher->items()->create([
                    'material_id' => $item->material_id,
                    'quantity'    => $item->quantity,
                    'notes'       => $item->notes,
                ]);
            }

            $voucher->post($user);

            $this->update([
                'status'                     => 'fulfilled',
                'fulfilled_stock_voucher_id' => $voucher->id,
            ]);

            return $voucher;
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
