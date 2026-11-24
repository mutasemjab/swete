<?php

namespace App\Support;

use App\Models\Document;
use App\Models\DocumentAssignment;
use App\Models\EquipmentUnit;
use Illuminate\Support\Collection;

/**
 * Computes which documents reach a unit ON READ — never by copying rows. A document assigned
 * once at the subtype/category/manufacturer/"all" level automatically reaches every unit under
 * it, including units added later, with zero extra database rows.
 */
class DocumentInheritance
{
    private const RANK = [
        'unit' => 0, 'model' => 1, 'subtype' => 2, 'category' => 3, 'manufacturer' => 4, 'all' => 5,
    ];

    /** Every non-archived document reaching this unit, most specific source first, de-duplicated. */
    public static function forUnit(EquipmentUnit $unit): Collection
    {
        $manufacturerId = $unit->model?->equipment_manufacturer_id;

        $assignments = DocumentAssignment::with('document.type')
            ->where(function ($q) use ($unit, $manufacturerId) {
                $q->where('equipment_unit_id', $unit->id)
                    ->orWhere('equipment_model_id', $unit->equipment_model_id)
                    ->orWhere('equipment_category_id', $unit->equipment_category_id)
                    ->orWhere('level', 'all');

                if ($unit->equipment_subtype_id) {
                    $q->orWhere('equipment_subtype_id', $unit->equipment_subtype_id);
                }
                if ($manufacturerId) {
                    $q->orWhere('equipment_manufacturer_id', $manufacturerId);
                }
            })
            ->whereHas('document', fn ($q) => $q->where('is_archived', false))
            ->get();

        // Keep only the single most-specific assignment per document (lowest rank wins).
        $best = [];
        foreach ($assignments as $assignment) {
            $documentId = $assignment->document_id;
            if (! isset($best[$documentId]) || self::RANK[$assignment->level] < self::RANK[$best[$documentId]->level]) {
                $best[$documentId] = $assignment;
            }
        }

        return collect($best)
            ->sortBy(fn (DocumentAssignment $a) => self::RANK[$a->level])
            ->map(function (DocumentAssignment $assignment) {
                $document = $assignment->document;
                $document->source_label = $assignment->scopeLabel();

                return $document;
            })
            ->values();
    }

    /** How many active units a given assignment currently reaches — shown as a sanity-check after saving. */
    public static function reachCount(DocumentAssignment $assignment): int
    {
        $query = EquipmentUnit::where('is_active', true);

        match ($assignment->level) {
            'unit'         => $query->where('id', $assignment->equipment_unit_id),
            'model'        => $query->where('equipment_model_id', $assignment->equipment_model_id),
            'subtype'      => $query->where('equipment_subtype_id', $assignment->equipment_subtype_id),
            'category'     => $query->where('equipment_category_id', $assignment->equipment_category_id),
            'manufacturer' => $query->whereHas('model', fn ($q) => $q->where('equipment_manufacturer_id', $assignment->equipment_manufacturer_id)),
            'all'          => null,
            default        => $query->whereRaw('0 = 1'),
        };

        return $query->count();
    }
}
