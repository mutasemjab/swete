<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\ModuleController;
use App\Models\ClientExtraField;
use Illuminate\Http\Request;

class ClientExtraFieldController extends ModuleController
{
    protected string $module = 'clients';

    public function customers()
    {
        $fields = ClientExtraField::forCustomers()->orderBy('sort_order')->orderBy('id')->get();

        return $this->moduleView('clients.extra-fields.index', [
            'fields'      => $fields,
            'entityType'  => 'customer',
            'fieldTypes'  => ClientExtraField::FIELD_TYPES,
        ]);
    }

    public function suppliers()
    {
        $fields = ClientExtraField::forSuppliers()->orderBy('sort_order')->orderBy('id')->get();

        return $this->moduleView('clients.extra-fields.index', [
            'fields'      => $fields,
            'entityType'  => 'supplier',
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

        $route = $validated['entity_type'] === 'customer'
            ? 'client-extra-fields.customers'
            : 'client-extra-fields.suppliers';

        return redirect()->route($route)->with('success', __('clients.extra_field_added'));
    }

    public function edit(ClientExtraField $clientExtraField)
    {
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

        $route = $clientExtraField->entity_type === 'customer'
            ? 'client-extra-fields.customers'
            : 'client-extra-fields.suppliers';

        return redirect()->route($route)->with('success', __('clients.extra_field_updated'));
    }

    public function destroy(ClientExtraField $clientExtraField)
    {
        $entityType = $clientExtraField->entity_type;
        $clientExtraField->delete();

        $route = $entityType === 'customer'
            ? 'client-extra-fields.customers'
            : 'client-extra-fields.suppliers';

        return redirect()->route($route)->with('success', __('clients.extra_field_deleted'));
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'entity_type' => ['required', 'in:customer,supplier'],
            'label_ar'    => ['required', 'string', 'max:150'],
            'label_en'    => ['nullable', 'string', 'max:150'],
            'field_type'  => ['required', 'in:' . implode(',', ClientExtraField::FIELD_TYPES)],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'status'      => ['boolean'],
        ]);
    }
}
