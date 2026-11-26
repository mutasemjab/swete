<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\ModuleController;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\EquipmentUnit;
use App\Models\MaintenanceContract;
use App\Models\MaintenanceContractUnit;
use App\Support\BackNavigation;
use App\Support\ContractVisitSync;
use App\Support\FileStorage;
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
            'emergency_visit_limit' => ['nullable', 'integer', 'min:0', 'max:100000'],
        ]);

        MaintenanceContract::create([
            'number'      => MaintenanceContract::nextNumber(),
            'customer_id' => $validated['customer_id'],
            'path'        => FileStorage::store($validated['file'], 'maintenance-contracts'),
            'signed_date' => $validated['signed_date'],
            'expiry_date' => $validated['expiry_date'],
            'notes'       => $validated['notes'] ?? null,
            'created_by'  => $request->user()->id,
            ...(\App\Support\MaintenanceFeatures::ready() ? ['emergency_visit_limit' => $validated['emergency_visit_limit'] ?? null] : []),
        ]);

        return redirect()->route('maintenance-contracts.index')
            ->with('success', __('maintenance.contract_added'));
    }

    public function show(MaintenanceContract $maintenanceContract)
    {
        $maintenanceContract->load(['customer', 'creator', 'payments.currency', 'payments.assignee', 'payments.invoice', 'activeUnits.unit', 'schedules.unit']);
        $currencies = Currency::where('status', true)->orderBy('name')->get();
        $backUrl = BackNavigation::url();

        return $this->moduleView('maintenance.contracts.show', [
            'contract'   => $maintenanceContract,
            'currencies' => $currencies,
            'backUrl'    => $backUrl,
        ]);
    }

    /** The customer's equipment units, with checkboxes for which ones this contract currently covers. */
    public function equipment(MaintenanceContract $maintenanceContract)
    {
        $units = EquipmentUnit::where('customer_id', $maintenanceContract->customer_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $coveredIds = $maintenanceContract->activeUnits()->pluck('equipment_unit_id')->all();

        return $this->moduleView('maintenance.contracts.equipment', [
            'contract'   => $maintenanceContract,
            'units'      => $units,
            'coveredIds' => $coveredIds,
            'backUrl'    => BackNavigation::url([$maintenanceContract], 'maintenance-contracts.show'),
        ]);
    }

    public function syncEquipment(Request $request, MaintenanceContract $maintenanceContract)
    {
        $chosenIds = collect($request->input('unit_ids', []))->map(fn ($id) => (int) $id)->all();
        $currentIds = $maintenanceContract->activeUnits()->pluck('equipment_unit_id')->all();

        $toRemove = array_diff($currentIds, $chosenIds);
        $toAdd = array_diff($chosenIds, $currentIds);

        if ($toRemove) {
            MaintenanceContractUnit::where('contract_id', $maintenanceContract->id)
                ->whereIn('equipment_unit_id', $toRemove)
                ->whereNull('removed_at')
                ->update(['removed_at' => now()]);
        }

        foreach ($toAdd as $unitId) {
            MaintenanceContractUnit::create([
                'contract_id'       => $maintenanceContract->id,
                'equipment_unit_id' => $unitId,
                'added_at'          => now(),
            ]);
        }

        ContractVisitSync::sync($maintenanceContract);

        return redirect()->route('maintenance-contracts.show', $maintenanceContract)
            ->with('success', __('maintenance.contract_equipment_updated'));
    }

    /** suspend / terminate / reactivate — the only three transitions a contract's status ever makes. */
    public function action(MaintenanceContract $maintenanceContract, string $verb)
    {
        abort_unless(in_array($verb, ['suspend', 'terminate', 'reactivate'], true), 404);

        $status = match ($verb) {
            'reactivate' => 'active',
            'suspend'    => 'suspended',
            'terminate'  => 'terminated',
        };
        $maintenanceContract->update(['status' => $status]);

        if ($verb === 'reactivate') {
            ContractVisitSync::sync($maintenanceContract);
        } else {
            ContractVisitSync::close($maintenanceContract);
        }

        return redirect()->route('maintenance-contracts.show', $maintenanceContract)
            ->with('success', __('maintenance.contract_status_'.$verb));
    }

    public function edit(MaintenanceContract $maintenanceContract)
    {
        $backUrl = BackNavigation::url([$maintenanceContract], 'maintenance-contracts.show');

        return $this->moduleView('maintenance.contracts.edit', [
            'contract'  => $maintenanceContract,
            'customers' => Customer::where('status', true)->orderBy('name')->get(),
            'backUrl'   => $backUrl,
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
            'emergency_visit_limit' => ['nullable', 'integer', 'min:0', 'max:100000'],
        ]);

        $path = $maintenanceContract->path;

        if ($request->hasFile('file')) {
            FileStorage::delete($path);
            $path = FileStorage::store($validated['file'], 'maintenance-contracts');
        }

        $maintenanceContract->update([
            'customer_id' => $validated['customer_id'],
            'path'        => $path,
            'signed_date' => $validated['signed_date'],
            'expiry_date' => $validated['expiry_date'],
            'notes'       => $validated['notes'] ?? null,
            ...(\App\Support\MaintenanceFeatures::ready() ? ['emergency_visit_limit' => $validated['emergency_visit_limit'] ?? null] : []),
        ]);

        return redirect()->route('maintenance-contracts.show', [$maintenanceContract, 'back' => $request->input('back')])
            ->with('success', __('maintenance.contract_updated'));
    }

    public function destroy(MaintenanceContract $maintenanceContract)
    {
        if (\App\Support\MaintenanceFeatures::ready() && \App\Models\MaintenanceVisit::where('contract_id', $maintenanceContract->id)->exists()) {
            return back()->with('error', __('maintenance.contract_has_visits'));
        }
        FileStorage::delete($maintenanceContract->path);

        $maintenanceContract->delete();

        return redirect()->route('maintenance-contracts.index')
            ->with('success', __('maintenance.contract_deleted'));
    }
}
