<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PriceAnalysis extends Model
{
    use LogsActivity;

    protected $fillable = [
        'number',
        'source_type',
        'branch_id',
        'tax_rate',
        'jd_rate',
        'with_tax',
        'notes',
        'linked_type',
        'linked_id',
        'created_by',
        'is_current',
        'version',
        'root_id',
    ];

    protected $casts = [
        'tax_rate'   => 'decimal:2',
        'jd_rate'    => 'decimal:4',
        'with_tax'   => 'boolean',
        'is_current' => 'boolean',
        'version'    => 'integer',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PriceAnalysisItem::class);
    }

    /** Only meaningful on a frozen/archived row — the still-editable live analysis it was cloned from. */
    public function root(): BelongsTo
    {
        return $this->belongsTo(self::class, 'root_id');
    }

    /** Only meaningful on the live (is_current=true) row — its frozen edit history, newest first. */
    public function versions(): HasMany
    {
        return $this->hasMany(self::class, 'root_id')->orderByDesc('version');
    }

    /** `linked_type` ('price_quote'|'project') says which table `linked_id` points into — same convention as Agreement::party(). */
    public function linked(): BelongsTo
    {
        return $this->linked_type === 'project'
            ? $this->belongsTo(Project::class, 'linked_id')
            : $this->belongsTo(PriceQuote::class, 'linked_id');
    }

    /** The stable "PA-YYYY-00001" part of the number, with its "-A{n}" revision suffix stripped off. */
    public function getBaseNumberAttribute(): string
    {
        return preg_replace('/-A\d+$/', '', $this->number);
    }

    /** A brand-new, distinct analysis always starts at revision A0. */
    public static function nextNumber(): string
    {
        $year  = now()->format('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;

        return sprintf('PA-%s-%05d-A0', $year, $count);
    }

    /**
     * Freezes the current DB state (header + items) into a new, read-only row and returns it —
     * called by the controller right before applying an edit, same shape as
     * PriceQuote::archiveCurrentVersion(). Since `number` is unique, the live row must move onto
     * its new revision number FIRST, before the frozen snapshot can take over the one it just vacated.
     */
    public function archiveCurrentVersion(): self
    {
        $this->loadMissing('items');

        $oldNumber  = $this->number;
        $oldVersion = $this->version;

        $this->update([
            'number'  => $this->base_number . '-A' . $oldVersion,
            'version' => $oldVersion + 1,
        ]);

        $archive = $this->replicate(['root_id']);
        $archive->number     = $oldNumber;
        $archive->is_current = false;
        $archive->version    = $oldVersion;
        $archive->root_id    = $this->id;
        $archive->save();

        foreach ($this->items as $item) {
            $archive->items()->create($item->toArray());
        }

        return $archive;
    }

    public function getSubtotalAttribute(): float
    {
        return (float) $this->items->sum(fn ($item) => $item->to_jd + $item->shipping);
    }

    /** The subtotal with tax added — equals the plain subtotal when `with_tax` is off, so callers never need to check the flag themselves. */
    public function getTotalWithTaxAttribute(): float
    {
        if (! $this->with_tax) {
            return $this->subtotal;
        }

        return round($this->subtotal * (1 + (float) $this->tax_rate / 100), 3);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
