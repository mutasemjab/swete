<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Where one Document reaches: exactly one of the equipment_* columns is set, matching `level`.
 * Computed on read (see App\Support\DocumentInheritance) — assigning a document once at the
 * subtype/category/manufacturer/"all" level makes it reach every current AND future unit under
 * it, with no extra rows and no file copies.
 */
class DocumentAssignment extends Model
{
    public const LEVELS = ['unit', 'model', 'subtype', 'category', 'manufacturer', 'all'];

    protected $fillable = [
        'document_id',
        'level',
        'equipment_unit_id',
        'equipment_model_id',
        'equipment_subtype_id',
        'equipment_category_id',
        'equipment_manufacturer_id',
        'created_by',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(EquipmentUnit::class, 'equipment_unit_id');
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(EquipmentModel::class, 'equipment_model_id');
    }

    public function subtype(): BelongsTo
    {
        return $this->belongsTo(EquipmentSubtype::class, 'equipment_subtype_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(EquipmentCategory::class, 'equipment_category_id');
    }

    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(EquipmentManufacturer::class, 'equipment_manufacturer_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Human label for "where this is assigned" — shown on the document list/edit page. */
    public function scopeLabel(): string
    {
        return match ($this->level) {
            'unit'         => __('equipment.doc_scope_unit', ['name' => $this->unit?->localized_name]),
            'model'        => __('equipment.doc_scope_model', ['name' => $this->model?->display_name]),
            'subtype'      => __('equipment.doc_scope_subtype', ['name' => $this->subtype?->localized_name]),
            'category'     => __('equipment.doc_scope_category', ['name' => $this->category?->localized_name]),
            'manufacturer' => __('equipment.doc_scope_manufacturer', ['name' => $this->manufacturer?->localized_name]),
            'all'          => __('equipment.doc_scope_all'),
            default        => '',
        };
    }
}
