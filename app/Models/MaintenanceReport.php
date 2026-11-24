<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/** One filled-out report — its `fields` are a permanent copy taken from the template at creation time. */
class MaintenanceReport extends Model
{
    use LogsActivity;

    protected $fillable = [
        'number',
        'template_id',
        'template_name',
        'material_id',
        'equipment_unit_id',
        'customer_id',
        'date',
        'problem',
        'solution',
        'notes',
        'materials_approval_status',
        'issue_voucher_id',
        'price_quote_id',
        'created_by',
        'visit_id',
        'content_status',
        'foreman_note',
        'foreman_reviewed_by',
        'foreman_reviewed_at',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'date'                => 'date',
        'foreman_reviewed_at' => 'datetime',
        'approved_at'         => 'datetime',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(MaintenanceReportTemplate::class, 'template_id');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function equipmentUnit(): BelongsTo
    {
        return $this->belongsTo(EquipmentUnit::class, 'equipment_unit_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(MaintenanceReportField::class, 'report_id')->orderBy('order');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(MaintenanceReportMaterial::class, 'report_id');
    }

    public function materialApprovals(): HasMany
    {
        return $this->hasMany(MaintenanceReportMaterialApproval::class, 'report_id');
    }

    public function issueVoucher(): BelongsTo
    {
        return $this->belongsTo(StockVoucher::class, 'issue_voucher_id');
    }

    public function priceQuote(): BelongsTo
    {
        return $this->belongsTo(PriceQuote::class);
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(MaintenanceVisit::class, 'visit_id');
    }

    public function foremanReviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'foreman_reviewed_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * received -> the foreman corrects and sends it on -> engineer -> the engineer approves ->
     * approved, permanently locked. Only the terminal lock is new behavior for existing reports —
     * see canEditContent().
     */
    public function sendToEngineer(User $user, ?string $note): void
    {
        $this->update([
            'content_status'      => 'engineer',
            'foreman_note'        => $note,
            'foreman_reviewed_by' => $user->id,
            'foreman_reviewed_at' => now(),
        ]);
    }

    public function approveContent(User $user): void
    {
        $this->update([
            'content_status' => 'approved',
            'approved_by'    => $user->id,
            'approved_at'    => now(),
        ]);
    }

    public function isContentApproved(): bool
    {
        return $this->content_status === 'approved';
    }

    /**
     * The only NEW restriction the content-approval workflow adds: once a report is actually
     * approved, it's a permanent record (same ethos as MaintenanceVisit::status=='completed').
     * Before that, today's existing `maintenance.reports.edit` permission keeps working exactly
     * as it always has — nothing about editing a 'received' or 'engineer' report changes.
     */
    public function canEditContent(User $user): bool
    {
        return ! $this->isContentApproved() && $user->can('maintenance.reports.edit');
    }

    public static function nextNumber(): string
    {
        $year  = now()->format('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;

        return sprintf('MR-%s-%05d', $year, $count);
    }

    /**
     * Snapshot the current approver pool into per-report decision rows and mark this report as
     * awaiting their sign-off. Called once, right after materials-used lines are saved for the
     * first time (never re-seeded on a later edit — see MaintenanceReportController).
     */
    public function seedMaterialApprovals(): void
    {
        $approverIds = MaintenanceReportMaterialApprover::pluck('user_id');

        if ($approverIds->isEmpty()) {
            return;
        }

        foreach ($approverIds as $userId) {
            $this->materialApprovals()->create(['user_id' => $userId]);
        }

        $this->update(['materials_approval_status' => 'pending']);
    }

    /** Record one approver's decision, then re-evaluate once everyone has responded. */
    public function recordMaterialDecision(User $user, string $decision, ?string $note = null): void
    {
        $approval = $this->materialApprovals()->where('user_id', $user->id)->where('decision', 'pending')->first();

        if (! $approval) {
            return;
        }

        $approval->update(['decision' => $decision, 'decided_at' => now(), 'note' => $note]);

        $this->resolveMaterialApprovalStatus($user);
    }

    private function resolveMaterialApprovalStatus(User $decidingUser): void
    {
        if ($this->materialApprovals()->where('decision', 'pending')->exists()) {
            return;
        }

        $rejected = $this->materialApprovals()->where('decision', 'rejected')->exists();

        $this->update(['materials_approval_status' => $rejected ? 'rejected' : 'approved']);

        if (! $rejected) {
            $this->createIssueVoucher($decidingUser);
        }
    }

    /**
     * Turn this report's unanimously-approved materials-used lines into a real, posted stock-issue
     * voucher — mirrors MaterialRequest::fulfill() almost verbatim. Left in 'approved' status with no
     * voucher if the warehouse doesn't actually have enough stock; nothing here is silently lost.
     */
    public function createIssueVoucher(User $user): ?StockVoucher
    {
        if ($this->issue_voucher_id || $this->materials->isEmpty()) {
            return $this->issueVoucher;
        }

        $warehouse = Warehouse::where('is_main', true)->where('status', true)->first()
            ?? Warehouse::where('status', true)->first();

        if (! $warehouse) {
            return null;
        }

        try {
            return DB::transaction(function () use ($user, $warehouse) {
                $voucher = StockVoucher::create([
                    'type'         => 'issue',
                    'number'       => StockVoucher::nextNumber('issue'),
                    'warehouse_id' => $warehouse->id,
                    'date'         => now()->toDateString(),
                    'reference_no' => $this->number,
                    'notes'        => __('maintenance.issue_voucher_note', ['number' => $this->number]),
                    'status'       => 'draft',
                    'created_by'   => $user->id,
                ]);

                foreach ($this->materials as $item) {
                    $voucher->items()->create([
                        'material_id' => $item->material_id,
                        'quantity'    => $item->quantity,
                    ]);
                }

                $voucher->post($user);

                $this->update(['issue_voucher_id' => $voucher->id]);

                return $voucher;
            });
        } catch (\RuntimeException $e) {
            // Insufficient stock (or any other posting failure) — leave the report approved but
            // without a voucher; whoever manages the warehouse can post one manually once stock is in.
            return null;
        }
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
