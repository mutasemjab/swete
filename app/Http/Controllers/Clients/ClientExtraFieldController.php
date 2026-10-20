<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\ModuleController;
use App\Models\ClientExtraField;
use Illuminate\Http\Request;

class ClientExtraFieldController extends ModuleController
{
    // One controller serves all entity types, each belonging to a different module now that
    // Customers/Suppliers/Tenders are separate modules — set which one per request, not a fixed property.
    protected string $module = 'customers';

    // entity_type ('customer'/'supplier'/'tender') -> its module key and route name are both just
    // the entity type pluralized, so every per-entity-type branch collapses to this one map.
    private const MODULES = [
        'customer' => 'customers',
        'supplier' => 'suppliers',
        'tender'   => 'tenders',
    ];

    public function customers()
    {
        $this->module = 'customers';
        $fields = ClientExtraField::forCustomers()->orderBy('sort_order')->orderBy('id')->get();

        return $this->moduleView('clients.extra-fields.index', [
            'fields'      => $fields,
            'entityType'  => 'customer',
            'fieldTypes'  => ClientExtraField::FIELD_TYPES,
        ]);
    }

    public function suppliers()
    {
        $this->module = 'suppliers';
        $fields = ClientExtraField::forSuppliers()->orderBy('sort_order')->orderBy('id')->get();

        return $this->moduleView('clients.extra-fields.index', [
            'fields'      => $fields,
            'entityType'  => 'supplier',
            'fieldTypes'  => ClientExtraField::FIELD_TYPES,
        ]);
    }

    public function tenders()
    {
        $this->module = 'tenders';
        $fields = ClientExtraField::forTenders()->orderBy('sort_order')->orderBy('id')->get();

        return $this->moduleView('clients.extra-fields.index', [
            'fields'      => $fields,
            'entityType'  => 'tender',
            'fieldTypes'  => ClientExtraField::FIELD_TYPES,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        ClientExtraField::create([
            ...$validated,
            'status' => $request->boolean('status', true),
        ]);

        return redirect()->route('client-extra-fields.' . self::MODULES[$validated['entity_type']])
            ->with('success', __('clients.extra_field_added'));
    }

    public function edit(ClientExtraField $clientExtraField)
    {
        $this->module = self::MODULES[$clientExtraField->entity_type];

        return $this->moduleView('clients.extra-fields.edit', [
            'field'      => $clientExtraField,
            'fieldTypes' => ClientExtraField::FIELD_TYPES,
        ]);
    }

    public function update(Request $request, ClientExtraField $clientExtraField)
    {
        $validated = $this->validated($request, $clientExtraField->id);

        $clientExtraField->update([
            ...$validated,
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('client-extra-fields.' . self::MODULES[$clientExtraField->entity_type])
            ->with('success', __('clients.extra_field_updated'));
    }

    public function destroy(ClientExtraField $clientExtraField)
    {
        $entityType = $clientExtraField->entity_type;
        $clientExtraField->delete();

        return redirect()->route('client-extra-fields.' . self::MODULES[$entityType])
            ->with('success', __('clients.extra_field_deleted'));
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'entity_type' => ['required', 'in:' . implode(',', array_keys(self::MODULES))],
            'label_ar'    => ['required', 'string', 'max:150'],
            'label_en'    => ['nullable', 'string', 'max:150'],
            'field_type'  => ['required', 'in:' . implode(',', ClientExtraField::FIELD_TYPES)],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'status'      => ['boolean'],
        ]);
    }
}
