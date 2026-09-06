<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\ModuleController;
use App\Models\Supplier;
use App\Models\SupplierGroup;
use Illuminate\Http\Request;

class SupplierController extends ModuleController
{
    protected string $module = 'accounting';

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
        $groups = SupplierGroup::orderBy('name')->get();
        return $this->moduleView('accounting.suppliers.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        $supplier = Supplier::create([
            ...$validated,
            'code'   => Supplier::nextCode(),
            'status' => $request->boolean('status', true),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['id' => $supplier->id, 'name' => $supplier->localized_name, 'code' => $supplier->code]);
        }

        return redirect()->route('accounting.suppliers.index')
            ->with('success', __('accounting.party_added'));
    }

    public function edit(Supplier $supplier)
    {
        $groups = SupplierGroup::orderBy('name')->get();
        return $this->moduleView('accounting.suppliers.edit', compact('supplier', 'groups'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $this->validated($request);

        $supplier->update([
            ...$validated,
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('accounting.suppliers.index')
            ->with('success', __('accounting.party_updated'));
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('accounting.suppliers.index')
            ->with('success', __('accounting.party_deleted'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'supplier_group_id' => ['nullable', 'exists:supplier_groups,id'],
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
