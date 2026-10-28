<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * The "X8" page's header — one per Price Analysis. Its line-by-line data lives in X8Item.
 *
 * X8 has its OWN independent revision chain (is_current/version/root_id), completely decoupled
 * from PriceAnalysis's own versioning — approving/rejecting/saving X8 only revisions X8 itself,
 * it never archives or touches the underlying PriceAnalysis or its items.
 */
class X8 extends Model
{
    protected $table = 'x8';

    protected $fillable = [
        'number',
        'version',
        'is_current',
        'root_id',
        'price_analysis_id',
        'created_by',
    ];

    protected $casts = [
        'version'    => 'integer',
        'is_current' => 'boolean',
    ];

    /**
     * A brand-new X8 header's number — a year-scoped counter, scoped to genesis rows only
     * (root_id IS NULL) so an archived revision clone (root_id set by archiveCurrentVersion())
     * never inflates the count — same fix already applied to PriceQuote/Project/PriceAnalysis's
     * own nextNumber().
     */
    public static function nextNumber(): string
    {
        $year  = now()->format('Y');
        $count = static::whereYear('created_at', $year)->whereNull('root_id')->count() + 1;

        return sprintf('X8-%s-%05d-R0', $year, $count);
    }

    /** The stable "X8-YYYY-00001" part of the number, with its "-R{n}" revision suffix stripped off. */
    public function getBaseNumberAttribute(): string
    {
        return preg_replace('/-R\d+$/', '', $this->number);
    }

    /** Gets this analysis's CURRENT X8 header, or creates one (with a fresh number) if it has none yet. */
    public static function getOrCreateFor(PriceAnalysis $analysis, int $createdBy): self
    {
        return static::where('price_analysis_id', $analysis->id)->where('is_current', true)->first()
            ?? static::create([
                'number'            => static::nextNumber(),
                'price_analysis_id' => $analysis->id,
                'created_by'        => $createdBy,
            ]);
    }

    public function priceAnalysis(): BelongsTo
    {
        return $this->belongsTo(PriceAnalysis::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(X8Item::class, 'x8_id');
    }

    /** Only meaningful on a frozen/archived row — the still-editable live header it was cloned from. */
    public function root(): BelongsTo
    {
        return $this->belongsTo(self::class, 'root_id');
    }

    /** Only meaningful on the live (is_current=true) row — its frozen decision history, newest first. */
    public function versions(): HasMany
    {
        return $this->hasMany(self::class, 'root_id')->orderByDesc('version');
    }

    /**
     * Freezes the current state (header + its items) into a new, read-only row and returns it —
     * called right before applying any approve/reject/save, same shape as
     * PriceQuote::archiveCurrentVersion(). Deliberately does NOT touch the underlying PriceAnalysis
     * at all — X8's own revisions are independent of the analysis's.
     */
    public function archiveCurrentVersion(): self
    {
        $this->loadMissing('items');

        $oldNumber  = $this->number;
        $oldVersion = $this->version;

        $this->update([
            'number'  => $this->base_number . '-R' . $oldVersion,
            'version' => $oldVersion + 1,
        ]);

        $archive = $this->replicate(['root_id']);
        $archive->number     = $oldNumber;
        $archive->is_current = false;
        $archive->version    = $oldVersion;
        $archive->root_id    = $this->id;
        $archive->save();

        foreach ($this->items as $x8Item) {
            $archive->items()->create($x8Item->toArray());
        }

        return $archive;
    }
}
