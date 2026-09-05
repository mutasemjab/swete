<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\ModuleController;
use App\Models\Material;
use App\Models\MaterialRequest;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class MaterialRequestController extends ModuleController
{
    protected string $module = 'warehouse';

    public function index()
    {
        $requests = MaterialRequest::with(['warehouse', 'requester'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return $this->moduleView('warehouse.material-requests.index', compact('requests'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('status', true)->orderBy('name')->get();
        $materials  = Material::where('status', true)->orderBy('name')->get();

        return $this->moduleView('warehouse.material-requests.create', compact('warehouses', 'materials'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id'          => ['required', 'exists:warehouses,id'],
            'needed_by_date'        => ['nullable', 'date'],
            'reason'                => ['nullable', 'string'],
            'items'                 => ['required', 'array', 'min:1'],
            'items.*.material_id'   => ['required', 'exists:materials,id'],
            'items.*.quantity'      => ['required', 'numeric', 'min:0.001'],
            'items.*.notes'         => ['nullable', 'string', 'max:255'],
        ]);

        $materialRequest = MaterialRequest::create([
            'number'         => MaterialRequest::nextNumber(),
            'warehouse_id'   => $validated['warehouse_id'],
            'requested_by'   => $request->user()->id,
            'needed_by_date' => $validated['needed_by_date'] ?? null,
            'reason'         => $validated['reason'] ?? null,
            'status'         => 'draft',
        ]);

        foreach ($validated['items'] as $item) {
            $materialRequest->items()->create($item);
        }

        return redirect()->route('warehouse.material-requests.show', $materialRequest)
            ->with('success', __('warehouse.material_request_added'));
    }

    public function show(MaterialRequest $materialRequest)
    {
        $materialRequest->load(['warehouse', 'requester', 'items.material.unit', 'approvals.requester', 'approvals.approver', 'fulfilledVoucher']);
        $approvers = User::where('status', true)->where('id', '!=', auth()->id())->orderBy('name')->get();

        return $this->moduleView('warehouse.material-requests.show', compact('materialRequest', 'approvers'));
    }

    public function requestApproval(Request $request, MaterialRequest $materialRequest)
    {
        $validated = $request->validate([
            'approver_id' => ['required', 'exists:users,id'],
            'note'        => ['nullable', 'string', 'max:1000'],
        ]);

        $approver = User::findOrFail($validated['approver_id']);

        $materialRequest->requestApproval($approver, 'default', $validated['note'] ?? null);
        $materialRequest->update(['status' => 'pending_approval']);

        return back()->with('success', __('warehouse.material_request_sent_for_approval'));
    }

    public function fulfill(Request $request, MaterialRequest $materialRequest)
    {
        try {
            $materialRequest->fulfill($request->user());
        } catch (\RuntimeException $e) {
            return back()->with('error', __($e->getMessage()));
        }

        return back()->with('success', __('warehouse.material_request_fulfilled'));
    }
}
