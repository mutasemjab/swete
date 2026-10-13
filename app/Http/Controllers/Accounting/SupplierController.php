<?php

namespace App\Http\Controllers\Accounting;

use App\Exports\SuppliersTemplateExport;
use App\Http\Controllers\ModuleController;
use App\Imports\SuppliersImport;
use App\Models\ClientExtraField;
use App\Models\ClientExtraFieldValue;
use App\Models\Country;
use App\Models\Governorate;
use App\Models\Supplier;
use App\Models\SupplierGroup;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Mews\Purifier\Facades\Purifier;

class SupplierController extends ModuleController
{
    protected string $module = 'suppliers';

    public function index(Request $request)
    {
        $query = Supplier::with('group');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('name_en', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%"));
        }

        $suppliers = $query->orderBy('name')->paginate(20)->withQueryString();

        return $this->moduleView('accounting.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        $groups       = SupplierGroup::orderBy('name')->get();
        $countries    = Country::where('status', true)->orderBy('name')->get();
        $governorates = Governorate::selectable();
        $extraFields  = ClientExtraField::forSuppliers()->active()->orderBy('sort_order')->get();
        $seedContacts = [['name' => '', 'phone' => '', 'job_title' => '']];

        return $this->moduleView('accounting.suppliers.create', compact('groups', 'countries', 'governorates', 'extraFields', 'seedContacts'));
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        $supplier = Supplier::create([
            ...$validated,
            'code'       => Supplier::nextCode(),
            'status'     => $request->boolean('status', true),
            'created_by' => $request->user()->id,
        ]);

        $this->syncContacts($supplier, $request->input('contacts', []));
        $this->syncExtraFieldValues('supplier', $supplier->id, $request);

        if ($request->wantsJson()) {
            return response()->json(['id' => $supplier->id, 'name' => $supplier->localized_name, 'code' => $supplier->code]);
        }

        return redirect()->route('accounting.suppliers.index')
            ->with('success', __('accounting.party_added'));
    }

    public function edit(Supplier $supplier)
    {
        $supplier->load('contacts');
        $groups       = SupplierGroup::orderBy('name')->get();
        $countries    = Country::where('status', true)->orderBy('name')->get();
        $governorates = Governorate::selectable($supplier->governorate_id);
        $extraFields  = ClientExtraField::forSuppliers()->active()->orderBy('sort_order')->get();
        $seedContacts = $supplier->contacts->map(fn ($c) => [
            'name'      => $c->name,
            'phone'     => $c->phone ?? '',
            'job_title' => $c->job_title ?? '',
        ])->all() ?: [['name' => '', 'phone' => '', 'job_title' => '']];
        $extraFieldValues = ClientExtraFieldValue::where('entity_type', 'supplier')
            ->where('entity_id', $supplier->id)
            ->get()
            ->keyBy('field_id');

        return $this->moduleView('accounting.suppliers.edit', compact('supplier', 'groups', 'countries', 'governorates', 'extraFields', 'seedContacts', 'extraFieldValues'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $this->validated($request);

        $supplier->update([
            ...$validated,
            'status' => $request->boolean('status'),
        ]);

        $this->syncContacts($supplier, $request->input('contacts', []));
        $this->syncExtraFieldValues('supplier', $supplier->id, $request);

        return redirect()->route('accounting.suppliers.index')
            ->with('success', __('accounting.party_updated'));
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('accounting.suppliers.index')
            ->with('success', __('accounting.party_deleted'));
    }

    public function importTemplate()
    {
        return Excel::download(new SuppliersTemplateExport(), 'suppliers-import-template.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        $import = new SuppliersImport($request->user()->id);
        Excel::import($import, $request->file('file'));

        if ($import->failures()->isNotEmpty()) {
            $rows = $import->failures()->map(fn ($f) => $f->row())->implode(', ');

            return back()->with('error', __('accounting.import_partial', ['rows' => $rows]));
        }

        return redirect()->route('accounting.suppliers.index')
            ->with('success', __('accounting.import_success'));
    }

    /** Image upload target for the shipping-instruction rich-text editor. */
    public function uploadShippingInstructionImage(Request $request)
    {
        $request->validate(['file' => ['required', 'image', 'max:4096']]);

        $filename = uploadImage('assets/uploads/suppliers/shipping-instructions', $request->file('file'));

        return response()->json([
            'location' => asset('assets/uploads/suppliers/shipping-instructions/' . $filename),
        ]);
    }

    private function syncContacts(Supplier $supplier, array $contacts): void
    {
        $supplier->contacts()->delete();

        $i = 0;
        foreach ($contacts as $row) {
            $name = trim($row['name'] ?? '');
            if ($name === '') {
                continue;
            }
            $supplier->contacts()->create([
                'name'       => $name,
                'phone'      => $row['phone'] ?: null,
                'job_title'  => $row['job_title'] ?: null,
                'sort_order' => $i++,
            ]);
        }
    }

    private function syncExtraFieldValues(string $entityType, int $entityId, Request $request): void
    {
        $fields = ClientExtraField::where('entity_type', $entityType)->active()->get();

        foreach ($fields as $field) {
            if ($field->field_type === 'image') {
                if ($request->hasFile("extra_fields_images.{$field->id}")) {
                    $filename = uploadImage(
                        'assets/uploads/clients/extra-fields',
                        $request->file("extra_fields_images.{$field->id}")
                    );
                    ClientExtraFieldValue::updateOrCreate(
                        ['field_id' => $field->id, 'entity_type' => $entityType, 'entity_id' => $entityId],
                        ['file_path' => "assets/uploads/clients/extra-fields/{$filename}", 'value' => null]
                    );
                }
            } else {
                $value = $request->input("extra_fields.{$field->id}");
                ClientExtraFieldValue::updateOrCreate(
                    ['field_id' => $field->id, 'entity_type' => $entityType, 'entity_id' => $entityId],
                    ['value' => $value ?: null, 'file_path' => null]
                );
            }
        }
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'supplier_group_id'       => ['nullable', 'exists:supplier_groups,id'],
            'name'                    => ['required', 'string', 'max:150'],
            'name_en'                 => ['nullable', 'string', 'max:150'],
            'company_name'            => ['nullable', 'string', 'max:200'],
            'company_name_en'         => ['nullable', 'string', 'max:200'],
            'commercial_registration' => ['nullable', 'string', 'max:100'],
            'personal_id'             => ['nullable', 'string', 'max:100'],
            'phone'                   => ['nullable', 'string', 'max:30'],
            'email'                   => ['nullable', 'email', 'max:150'],
            'address'                 => ['nullable', 'string', 'max:255'],
            'tax_number'              => ['nullable', 'string', 'max:50'],
            'opening_balance'         => ['nullable', 'numeric'],
            'location_scope'          => ['nullable', 'in:inside_jordan,outside_jordan'],
            'governorate_id'          => ['required_if:location_scope,inside_jordan', 'nullable', 'exists:governorates,id'],
            'country_id'              => ['required_if:location_scope,outside_jordan', 'nullable', 'exists:countries,id'],
            'shipping_instruction'    => ['nullable', 'string'],
            'status'                  => ['boolean'],
            'contacts'                => ['nullable', 'array'],
            'contacts.*.name'         => ['nullable', 'string', 'max:150'],
            'contacts.*.phone'        => ['nullable', 'string', 'max:30'],
            'contacts.*.job_title'    => ['nullable', 'string', 'max:100'],
            'extra_fields'            => ['nullable', 'array'],
            'extra_fields.*'          => ['nullable', 'string'],
            'extra_fields_images'     => ['nullable', 'array'],
            'extra_fields_images.*'   => ['nullable', 'image', 'max:4096'],
        ]);

        if (($validated['location_scope'] ?? null) === 'outside_jordan') {
            $validated['governorate_id'] = null;
        } elseif (($validated['location_scope'] ?? null) === 'inside_jordan') {
            $validated['country_id'] = null;
        }

        if (! empty($validated['shipping_instruction'])) {
            $validated['shipping_instruction'] = Purifier::clean($validated['shipping_instruction'], 'rich_text');
        }

        return $validated;
    }
}
