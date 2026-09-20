<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Tender extends Model
{
    use LogsActivity;

    public const DELIVERY_TERMS = ['site', 'cfr', 'exwork'];

    public const COVERAGE_OPTIONS = ['supply', 'supply_execution', 'design', 'design_execution', 'design_supply_execution'];

    protected $fillable = [
        'number',
        'party_id',
        'title',
        'title_en',
        'entity_name',
        'entity_name_en',
        'location_scope',
        'governorate_id',
        'country_id',
        'tax_exempt',
        'customs_exempt',
        'delivery_terms',
        'coverage',
        'currency_id',
        'description',
        'win_probability',
        'submission_deadline',
        'status_id',
        'documents_url',
        'design_documents_url',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'submission_deadline' => 'date',
        'win_probability'     => 'integer',
        'tax_exempt'          => 'boolean',
        'customs_exempt'      => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'party_id');
    }

    public function statusRef(): BelongsTo
    {
        return $this->belongsTo(TenderStatus::class, 'status_id');
    }

    public function priceQuotes(): HasMany
    {
        return $this->hasMany(PriceQuote::class);
    }

    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function getLocalizedTitleAttribute(): string
    {
        return app()->isLocale('en') && $this->title_en
            ? $this->title_en
            : $this->title;
    }

    public function getLocalizedEntityNameAttribute(): string
    {
        return app()->isLocale('en') && $this->entity_name_en
            ? $this->entity_name_en
            : $this->entity_name;
    }

    public function getLocalizedGovernorateAttribute(): ?string
    {
        return $this->governorate?->localized_name;
    }

    public static function nextNumber(): string
    {
        $year  = now()->format('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;

        return sprintf('TND-%s-%05d', $year, $count);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
