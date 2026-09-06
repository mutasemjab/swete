<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\ModuleController;
use App\Models\Customer;
use App\Models\CustomerGroup;
use Illuminate\Http\Request;

class CustomerController extends ModuleController
{
    protected string $module = 'accounting';

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
        $groups = CustomerGroup::orderBy('name')->get();
        return $this->moduleView('accounting.customers.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        $customer = Customer::create([
            ...$validated,
            'code'   => Customer::nextCode(),
            'status' => $request->boolean('status', true),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['id' => $customer->id, 'name' => $customer->localized_name, 'code' => $customer->code]);
        }

        return redirect()->route('accounting.customers.index')
            ->with('success', __('accounting.party_added'));
    }

    public function edit(Customer $customer)
    {
        $groups = CustomerGroup::orderBy('name')->get();
        return $this->moduleView('accounting.customers.edit', compact('customer', 'groups'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $this->validated($request);

        $customer->update([
            ...$validated,
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('accounting.customers.index')
            ->with('success', __('accounting.party_updated'));
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('accounting.customers.index')
            ->with('success', __('accounting.party_deleted'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'customer_group_id' => ['nullable', 'exists:customer_groups,id'],
            'name'              => ['required', 'string', 'max:150'],
            'name_en'           => ['nullable', 'string', 'max:150'],
            'phone'             => ['nullable', 'string', 'max:30'],
            'email'             => ['nullable', 'email', 'max:150'],
            'address'           => ['nullable', 'string', 'max:255'],
            'tax_number'        => ['nullable', 'string', 'max:50'],
            'opening_balance'   => ['nullable', 'numeric'],
            'status'            => ['boolean'],
        ]);
    }
}
