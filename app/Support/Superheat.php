<?php

namespace App\Support;

use App\Models\EquipmentUnit;
use App\Models\RefrigerantPtPoint;

/**
 * Superheat = suction temperature minus the refrigerant's saturation temperature at the
 * suction pressure. The saturation pressure-temperature relationship is NOT hardcoded here —
 * it's read from RefrigerantPtPoint rows an engineer enters from their own refrigerant
 * manufacturer's real P-T chart (see that table's migration comment for why). Only the
 * interpolation METHOD and the unit-conversion constants below are fixed, universal facts.
 */
class Superheat
{
    // Default "normal" superheat band in Kelvin — a widely-cited general field guideline, not a
    // refrigerant-specific physical constant. A unit's own reading_limits override wins over its
    // model's, which wins over this default — see bandForUnit().
    private const DEFAULT_LOW = 4.0;
    private const DEFAULT_HIGH = 12.0;

    private const ATMOSPHERE_BAR = 1.01325;

    // Exact, universal unit-conversion factors — not refrigerant-specific data.
    private const TO_BAR = [
        'bar' => 1.0,
        'psi' => 1 / 14.5037738,
        'kpa' => 0.01,
        'mpa' => 10.0,
    ];

    public static function availableRefrigerants(): array
    {
        return RefrigerantPtPoint::select('refrigerant')->distinct()->orderBy('refrigerant')->pluck('refrigerant')->all();
    }

    public static function convertToBar(float $pressure, string $unit): float
    {
        $factor = self::TO_BAR[strtolower($unit)] ?? null;

        if ($factor === null) {
            throw new \InvalidArgumentException("Unknown pressure unit: {$unit}");
        }

        return $pressure * $factor;
    }

    /**
     * Log-linear interpolation between the two real chart points bracketing the given pressure
     * (ln(pressure) is close to a straight line over a small temperature step, so this stays
     * accurate between adjacent entered points without needing a formula for the whole curve).
     * Returns null if there isn't at least one point on each side of the given pressure.
     */
    public static function saturationTemp(string $refrigerant, float $pressureBarAbsolute): ?float
    {
        $points = RefrigerantPtPoint::where('refrigerant', $refrigerant)
            ->orderBy('pressure_bar')
            ->get(['temperature_c', 'pressure_bar']);

        if ($points->count() < 2) {
            return null;
        }

        $lower = $points->last(fn ($p) => $p->pressure_bar <= $pressureBarAbsolute);
        $upper = $points->first(fn ($p) => $p->pressure_bar >= $pressureBarAbsolute);

        if (! $lower || ! $upper || $lower->pressure_bar === $upper->pressure_bar) {
            // Outside the entered chart's range, or an exact single match.
            $exact = $points->first(fn ($p) => abs($p->pressure_bar - $pressureBarAbsolute) < 0.0001);
            return $exact?->temperature_c;
        }

        $ratio = (log($pressureBarAbsolute) - log($lower->pressure_bar)) / (log($upper->pressure_bar) - log($lower->pressure_bar));

        return $lower->temperature_c + $ratio * ($upper->temperature_c - $lower->temperature_c);
    }

    /** [low, high] in Kelvin — unit override > model override > the general default above. */
    public static function bandForUnit(?EquipmentUnit $unit): array
    {
        $unitBand = $unit?->reading_limits['superheat'] ?? null;
        if (is_array($unitBand) && isset($unitBand['low'], $unitBand['high'])) {
            return [(float) $unitBand['low'], (float) $unitBand['high']];
        }

        $modelBand = $unit?->model?->reading_limits['superheat'] ?? null;
        if (is_array($modelBand) && isset($modelBand['low'], $modelBand['high'])) {
            return [(float) $modelBand['low'], (float) $modelBand['high']];
        }

        return [self::DEFAULT_LOW, self::DEFAULT_HIGH];
    }

    /**
     * @return array{superheat: ?float, zone: ?string, saturation_temp: ?float, message: ?string}
     */
    public static function calculate(string $refrigerant, float $suctionTempC, float $suctionPressure, string $pressureUnit, array $band): array
    {
        $gaugeBar = self::convertToBar($suctionPressure, $pressureUnit);
        $absoluteBar = $gaugeBar + self::ATMOSPHERE_BAR;

        $saturationTemp = self::saturationTemp($refrigerant, $absoluteBar);

        if ($saturationTemp === null) {
            return [
                'superheat'       => null,
                'zone'            => null,
                'saturation_temp' => null,
                'message'         => __('equipment.superheat_no_chart_data', ['refrigerant' => $refrigerant]),
            ];
        }

        $superheat = $suctionTempC - $saturationTemp;
        [$low, $high] = $band;
        $zone = $superheat < $low ? 'low' : ($superheat > $high ? 'high' : 'normal');

        return [
            'superheat'       => round($superheat, 2),
            'zone'            => $zone,
            'saturation_temp' => round($saturationTemp, 2),
            'message'         => null,
        ];
    }
}
