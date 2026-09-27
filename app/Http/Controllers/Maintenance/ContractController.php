<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\ModuleController;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\MaintenanceContract;
use Illuminate\Http\Request;

class ContractController extends ModuleController
{
    protected string $module = 'maintenance';

    public function index(Request $request)
    {
        $query = MaintenanceContract::with('customer');

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }

        $contracts = $query->orderBy('expiry_date')->paginate(20)->withQueryString();
        $customers = Customer::where('status', true)->orderBy('name')->get();

        return $this->moduleView('maintenance.contracts.index', compact('contracts', 'customers'));
    }

    public function create()
    {
        return $this->moduleView('maintenance.contracts.create', [
            'contract'  => null,
            'customers' => Customer::where('status', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id'  => ['required', 'exists:customers,id'],
            'file'         => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'signed_date'  => ['required', 'date'],
            'expiry_date'  => ['required', 'date', 'after_or_equal:signed_date'],
            'notes'        => ['nullable', 'string'],
        ]);

        $filename = uploadImage('assets/uploads/maintenance-contracts', $validated['file']);

        MaintenanceContract::create([
            'number'      => MaintenanceContract::nextNumber(),
            'customer_id' => $validated['customer_id'],
            'path'        => 'assets/uploads/maintenance-contracts/' . $filename,
            'signed_date' => $validated['signed_date'],
            'expiry_date' => $validated['expiry_date'],
            'notes'       => $validated['notes'] ?? null,
            'created_by'  => $request->user()->id,
        ]);

        return redirect()->route('maintenance-contracts.index')
            ->with('success', __('maintenance.contract_added'));
    }

    public function show(MaintenanceContract $maintenanceContract)
    {
        $maintenanceContract->load(['customer', 'creator', 'payments.currency', 'payments.assignee', 'payments.invoice']);
        $currencies = Currency::where('status', true)->orderBy('name')->get();

        return $this->moduleView('maintenance.contracts.show', [
            'contract'   => $maintenanceContract,
            'currencies' => $currencies,
        ]);
    }

    public function edit(MaintenanceContract $maintenanceContract)
    {
        return $this->moduleView('maintenance.contracts.edit', [
            'contract'  => $maintenanceContract,
            'customers' => Customer::where('status', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, MaintenanceContract $maintenanceContract)
    {
        $validated = $request->validate([
            'customer_id'  => ['required', 'exists:customers,id'],
            'file'         => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'signed_date'  => ['required', 'date'],
            'expiry_date'  => ['required', 'date', 'after_or_equal:signed_date'],
            'notes'        => ['nullable', 'string'],
        ]);

        $path = $maintenanceContract->path;

        if ($request->hasFile('file')) {
            if (file_exists(base_path($path))) {
                @unlink(base_path($path));
            }

            $path = 'assets/uploads/maintenance-contracts/' . uploadImage('assets/uploads/maintenance-contracts', $validated['file']);
        }

        $maintenanceContract->update([
            'customer_id' => $validated['customer_id'],
            'path'        => $path,
            'signed_date' => $validated['signed_date'],
            'expiry_date' => $validated['expiry_date'],
            'notes'       => $validated['notes'] ?? null,
        ]);

        return redirect()->route('maintenance-contracts.show', $maintenanceContract)
            ->with('success', __('maintenance.contract_updated'));
    }

    public function destroy(MaintenanceContract $maintenanceContract)
    {
        if (file_exists(base_path($maintenanceContract->path))) {
            @unlink(base_path($maintenanceContract->path));
        }

        $maintenanceContract->delete();

        return redirect()->route('maintenance-contracts.index')
            ->with('success', __('maintenance.contract_deleted'));
    }
}
