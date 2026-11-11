<?php

namespace App\Models;

use App\Support\FileStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class ClientExtraFieldValue extends Model
{
    protected $fillable = ['field_id', 'entity_type', 'entity_id', 'value', 'file_path'];

    public function field(): BelongsTo
    {
        return $this->belongsTo(ClientExtraField::class, 'field_id');
    }

    public function getFileUrlAttribute(): ?string
    {
        return FileStorage::url($this->file_path);
    }

    /**
     * Upserts every active extra-field value for one entity (customer/supplier/tender) from a
     * single request — shared by SupplierController, CustomerController and TenderController,
     * which all drove this exact same logic independently before.
     */
    public static function syncFor(string $entityType, int $entityId, Request $request): void
    {
        $fields = ClientExtraField::where('entity_type', $entityType)->active()->get();

        foreach ($fields as $field) {
            if ($field->field_type === 'image') {
                if ($request->hasFile("extra_fields_images.{$field->id}")) {
                    static::updateOrCreate(
                        ['field_id' => $field->id, 'entity_type' => $entityType, 'entity_id' => $entityId],
                        [
                            'file_path' => FileStorage::store($request->file("extra_fields_images.{$field->id}"), 'clients/extra-fields'),
                            'value'     => null,
                        ]
                    );
                }
            } else {
                $value = $request->input("extra_fields.{$field->id}");
                static::updateOrCreate(
                    ['field_id' => $field->id, 'entity_type' => $entityType, 'entity_id' => $entityId],
                    ['value' => $value ?: null, 'file_path' => null]
                );
            }
        }
    }
}
