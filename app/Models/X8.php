<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** The "X8" page's header — one per Price Analysis. Its line-by-line data lives in X8Item. */
class X8 extends Model
{
    protected $table = 'x8';

    protected $fillable = [
        'number',
        'price_analysis_id',
        'created_by',
    ];

    /**
     * A brand-new X8 header's number — a year-scoped counter. Only ever called once, right when a
     * header is first created; archiveCurrentVersion() clones an EXISTING header's `number`
     * unchanged onto every archived revision rather than generating a new one, so — same bug class
     * already fixed on PriceQuote/Project/PriceAnalysis's own nextNumber() — those clones must be
     * excluded from the count here too, or the next genuinely NEW header skips numbers. Since X8
     * has no root_id/is_current of its own, exclude them indirectly: at any moment, exactly one X8
     * row points at a given logical analysis's CURRENT (is_current=true) revision — every archived
     * clone points at an archived (is_current=false) one instead.
     */
    public static function nextNumber(): string
    {
        $year  = now()->format('Y');
        $count = static::whereYear('created_at', $year)
            ->whereHas('priceAnalysis', fn ($q) => $q->where('is_current', true))
            ->count() + 1;

        return sprintf('X8-%s-%05d', $year, $count);
    }

    /** Gets this analysis's existing X8 header, or creates one (with a fresh number) if it has none yet. */
    public static function getOrCreateFor(PriceAnalysis $analysis, int $createdBy): self
    {
        return static::where('price_analysis_id', $analysis->id)->first()
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
}
