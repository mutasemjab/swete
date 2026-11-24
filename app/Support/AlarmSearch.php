<?php

namespace App\Support;

use App\Models\AlarmCode;
use Illuminate\Support\Collection;

/**
 * Deterministic alarm lookup — NOT an AI/LLM assistant. Tries an exact/fuzzy code match first
 * (matches the HVAC source app's own confirmed design: its "Alarm Lookup" is plain text search
 * over stored alarm codes, never a generative model), then falls back to keyword search across
 * the stored causes/checks/effect/action text. A query with few matching words gets no answer
 * rather than a wrong one — same precision-over-recall rule as the source app.
 */
class AlarmSearch
{
    public static function search(string $query, ?int $controlSystemId = null): Collection
    {
        $query = trim($query);
        if ($query === '') {
            return collect();
        }

        $byCode = self::findByCode($query, $controlSystemId);
        if ($byCode->isNotEmpty()) {
            return $byCode;
        }

        return self::findByKeywords($query, $controlSystemId);
    }

    /** Exact normalized-key match first, then a substring fallback on code/name. */
    public static function findByCode(string $query, ?int $controlSystemId = null): Collection
    {
        $key = AlarmCode::normalizeKey($query);

        if ($key === '') {
            return collect();
        }

        $base = AlarmCode::with(['controlSystem', 'sourceDocument']);
        if ($controlSystemId) {
            $base->where('control_system_id', $controlSystemId);
        }

        $exact = (clone $base)->where('key', $key)->get();
        if ($exact->isNotEmpty()) {
            return $exact;
        }

        // Only treat it as a code search if it actually looks like one (short, mostly alphanumeric)
        // — otherwise a plain sentence like "fan does not start" would wrongly key-match nothing
        // and skip straight past the real keyword search below.
        if (mb_strlen($key) > 12 || mb_strlen($key) < 2) {
            return collect();
        }

        return (clone $base)
            ->where(fn ($q) => $q->where('key', 'like', "%{$key}%")
                ->orWhere('code', 'like', "%{$query}%"))
            ->get();
    }

    /** Keyword search across the stored text — requires at least 60% of the typed words to match. */
    public static function findByKeywords(string $query, ?int $controlSystemId = null): Collection
    {
        $words = collect(preg_split('/\s+/', mb_strtolower($query)))
            ->map(fn ($w) => trim($w, ".,;:!؟?"))
            ->filter(fn ($w) => mb_strlen($w) >= 2)
            ->unique()
            ->values();

        if ($words->isEmpty()) {
            return collect();
        }

        $columns = ['name_en', 'name_ar', 'causes_en', 'causes_ar', 'checks_en', 'checks_ar', 'effect_en', 'effect_ar', 'action_en', 'action_ar'];

        $base = AlarmCode::with(['controlSystem', 'sourceDocument']);
        if ($controlSystemId) {
            $base->where('control_system_id', $controlSystemId);
        }

        $candidates = (clone $base)
            ->where(function ($q) use ($words, $columns) {
                foreach ($words as $word) {
                    foreach ($columns as $column) {
                        $q->orWhere($column, 'like', "%{$word}%");
                    }
                }
            })
            ->get();

        $minMatches = max(min(2, $words->count()), (int) ceil($words->count() * 0.6));

        return $candidates->filter(function (AlarmCode $alarm) use ($words, $columns, $minMatches) {
            $haystack = mb_strtolower(collect($columns)->map(fn ($c) => $alarm->{$c})->join(' '));
            $matched = $words->filter(fn ($word) => str_contains($haystack, $word))->count();

            return $matched >= $minMatches;
        })->values();
    }
}
