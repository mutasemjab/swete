<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\ModuleController;
use App\Models\ClientExtraField;
use App\Models\ClientExtraFieldValue;
use App\Models\Customer;
use App\Models\CustomerGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends ModuleController
{
    protected string $module = 'customers';

    public function index(Request $request)
    {
        $query = Customer::with('group');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('name_en', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%"));
        }

        $customers = $query->orderBy('name')->paginate(20)->withQueryString();

        return $this->moduleView('accounting.customers.index', compact('customers'));
    }

    public function create()
    {
        $groups       = CustomerGroup::orderBy('name')->get();
        $extraFields  = ClientExtraField::forCustomers()->active()->orderBy('sort_order')->get();
        $seedContacts = [['name' => '', 'phone' => '', 'job_title' => '']];

        return $this->moduleView('accounting.customers.create', compact('groups', 'extraFields', 'seedContacts'));
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $customer = Customer::create([
            ...$validated,
            'code'       => Customer::nextCode(),
            'status'     => $request->boolean('status', true),
            'created_by' => $request->user()->id,
        ]);

        $this->syncContacts($customer, $request->input('contacts', []));
        $this->syncExtraFieldValues('customer', $customer->id, $request);

        if ($request->wantsJson()) {
            return response()->json(['id' => $customer->id, 'name' => $customer->localized_name, 'code' => $customer->code]);
        }

        return redirect()->route('accounting.customers.index')
            ->with('success', __('accounting.party_added'));
    }

    public function edit(Customer $customer)
    {
        $customer->load('contacts');
        $groups       = CustomerGroup::orderBy('name')->get();
        $extraFields  = ClientExtraField::forCustomers()->active()->orderBy('sort_order')->get();
        $seedContacts = $customer->contacts->map(fn ($c) => [
            'name'      => $c->name,
            'phone'     => $c->phone ?? '',
            'job_title' => $c->job_title ?? '',
        ])->all() ?: [['name' => '', 'phone' => '', 'job_title' => '']];
        $extraFieldValues = ClientExtraFieldValue::where('entity_type', 'customer')
            ->where('entity_id', $customer->id)
            ->get()
            ->keyBy('field_id');

        return $this->moduleView('accounting.customers.edit', compact('customer', 'groups', 'extraFields', 'seedContacts', 'extraFieldValues'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $this->validated($request);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $customer->update([
            ...$validated,
            'status' => $request->boolean('status'),
        ]);

        $this->syncContacts($customer, $request->input('contacts', []));
        $this->syncExtraFieldValues('customer', $customer->id, $request);

        return redirect()->route('accounting.customers.index')
            ->with('success', __('accounting.party_updated'));
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('accounting.customers.index')
            ->with('success', __('accounting.party_deleted'));
    }

    private function syncContacts(Customer $customer, array $contacts): void
    {
        $customer->contacts()->delete();

        $i = 0;
        foreach ($contacts as $row) {
            $name = trim($row['name'] ?? '');
            if ($name === '') {
                continue;
            }
            $customer->contacts()->create([
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
        return $request->validate([
            'customer_group_id'        => ['nullable', 'exists:customer_groups,id'],
            'name'                     => ['required', 'string', 'max:150'],
            'name_en'                  => ['nullable', 'string', 'max:150'],
            'company_name'             => ['nullable', 'string', 'max:200'],
            'company_name_en'          => ['nullable', 'string', 'max:200'],
            'commercial_registration'  => ['nullable', 'string', 'max:100'],
            'personal_id'              => ['nullable', 'string', 'max:100'],
            'phone'                    => ['nullable', 'string', 'max:30'],
            'email'                    => ['nullable', 'email', 'max:150'],
            'address'                  => ['nullable', 'string', 'max:255'],
            'tax_number'               => ['nullable', 'string', 'max:50'],
            'opening_balance'          => ['nullable', 'numeric'],
            'shipping_address_line1'    => ['nullable', 'string', 'max:255'],
            'shipping_address_line1_en' => ['nullable', 'string', 'max:255'],
            'shipping_po_box'           => ['nullable', 'string', 'max:30'],
            'shipping_postal_code'      => ['nullable', 'string', 'max:30'],
            'shipping_city'             => ['nullable', 'string', 'max:100'],
            'shipping_city_en'          => ['nullable', 'string', 'max:100'],
            'shipping_country'          => ['nullable', 'string', 'max:100'],
            'shipping_country_en'       => ['nullable', 'string', 'max:100'],
            'status'                   => ['boolean'],
            'password'                 => ['nullable', 'string', 'min:6'],
            'contacts'                 => ['nullable', 'array'],
            'contacts.*.name'          => ['nullable', 'string', 'max:150'],
            'contacts.*.phone'         => ['nullable', 'string', 'max:30'],
            'contacts.*.job_title'     => ['nullable', 'string', 'max:100'],
            'extra_fields'             => ['nullable', 'array'],
            'extra_fields.*'           => ['nullable', 'string'],
            'extra_fields_images'      => ['nullable', 'array'],
            'extra_fields_images.*'    => ['nullable', 'image', 'max:4096'],
        ]);
    }
}
