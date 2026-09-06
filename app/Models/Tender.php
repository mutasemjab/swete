<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Tender extends Model
{
    use LogsActivity;

    /** Jordan's 12 governorates — a fixed reference list, not admin-managed data. */
    public const JORDAN_GOVERNORATES = [
        'amman'    => ['ar' => 'عمّان',    'en' => 'Amman'],
        'irbid'    => ['ar' => 'إربد',     'en' => 'Irbid'],
        'zarqa'    => ['ar' => 'الزرقاء',   'en' => 'Zarqa'],
        'balqa'    => ['ar' => 'البلقاء',   'en' => 'Balqa'],
        'mafraq'   => ['ar' => 'المفرق',    'en' => 'Mafraq'],
        'karak'    => ['ar' => 'الكرك',     'en' => 'Karak'],
        'jerash'   => ['ar' => 'جرش',      'en' => 'Jerash'],
        'ajloun'   => ['ar' => 'عجلون',    'en' => 'Ajloun'],
        'madaba'   => ['ar' => 'مادبا',     'en' => 'Madaba'],
        'tafilah'  => ['ar' => 'الطفيلة',   'en' => 'Tafilah'],
        'maan'     => ['ar' => 'معان',     'en' => 'Ma\'an'],
        'aqaba'    => ['ar' => 'العقبة',    'en' => 'Aqaba'],
    ];

    protected $fillable = [
        'number',
        'type',
        'party_id',
        'title',
        'title_en',
        'entity_name',
        'entity_name_en',
        'location_scope',
        'governorate',
        'description',
        'win_probability',
        'submission_deadline',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'submission_deadline' => 'date',
        'win_probability'     => 'integer',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'party_id');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
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
        if (! $this->governorate || ! isset(self::JORDAN_GOVERNORATES[$this->governorate])) {
            return null;
        }

        return self::JORDAN_GOVERNORATES[$this->governorate][app()->isLocale('en') ? 'en' : 'ar'];
    }

    public static function nextNumber(string $type): string
    {
        $prefix = $type === 'service_call' ? 'SRV' : 'TND';
        $year   = now()->format('Y');
        $count  = static::ofType($type)->whereYear('created_at', $year)->count() + 1;

        return sprintf('%s-%s-%05d', $prefix, $year, $count);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
