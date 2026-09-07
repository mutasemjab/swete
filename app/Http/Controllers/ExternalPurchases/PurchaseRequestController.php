<?php

namespace App\Http\Controllers\ExternalPurchases;

use App\Http\Controllers\ModuleController;
use App\Models\Branch;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Material;
use App\Models\Project;
use App\Models\PurchaseRequest;
use App\Models\ServiceCall;
use App\Models\Supplier;
use App\Models\Tender;
use Illuminate\Http\Request;

class PurchaseRequestController extends ModuleController
{
    protected string $module = 'external_purchases';

    public function index(Request $request)
    {
        $query = PurchaseRequest::with(['supplier', 'branch', 'project', 'serviceCall']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('number', 'like', "%{$search}%");
        }

        $purchaseRequests = $query->latest()->paginate(20)->withQueryString();

        return $this->moduleView('external-purchases.purchase-requests.index', compact('purchaseRequests'));
    }

    public function create(Request $request)
    {
        $project     = $request->filled('project_id') ? Project::find($request->input('project_id')) : null;
        $serviceCall = $request->filled('service_call_id') ? ServiceCall::find($request->input('service_call_id')) : null;

        return $this->moduleView('external-purchases.purchase-requests.create', [
            ...$this->formOptions(),
            'purchaseRequest' => null,
            'project'         => $project,
            'serviceCall'     => $serviceCall,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        $purchaseRequest = PurchaseRequest::create([
            ...$validated,
            'number'     => PurchaseRequest::nextNumber(),
            'created_by' => $request->user()->id,
        ]);

        $this->syncItems($purchaseRequest, $validated['items']);

        return redirect()->route('purchase-requests.show', $purchaseRequest)
            ->with('success', __('external_purchases.request_added'));
    }

    public function show(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->load(['supplier', 'branch', 'project', 'serviceCall', 'country', 'currency', 'creator', 'items.material.unit']);

        return $this->moduleView('external-purchases.purchase-requests.show', compact('purchaseRequest'));
    }

    public function edit(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->load('items');

        return $this->moduleView('external-purchases.purchase-requests.edit', [
            ...$this->formOptions(),
            'purchaseRequest' => $purchaseRequest,
            'project'         => $purchaseRequest->project,
            'serviceCall'     => $purchaseRequest->serviceCall,
        ]);
    }

    public function update(Request $request, PurchaseRequest $purchaseRequest)
    {
        $validated = $this->validated($request);

        $purchaseRequest->update($validated);

        $this->syncItems($purchaseRequest, $validated['items']);

        return redirect()->route('purchase-requests.show', $purchaseRequest)
            ->with('success', __('external_purchases.request_updated'));
    }

    public function destroy(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->delete();

        return redirect()->route('purchase-requests.index')
            ->with('success', __('external_purchases.request_deleted'));
    }

    /** Standalone, print-optimized A4 document — deliberately not wrapped in the app shell. */
    public function printDocument(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->load(['supplier', 'branch', 'project', 'serviceCall', 'country', 'currency', 'creator', 'items.material.unit']);

        return view('external-purchases.purchase-requests.print', compact('purchaseRequest'));
    }

    private function formOptions(): array
    {
        return [
            'projects'     => Project::where('status', 'active')->orderByDesc('created_at')->get(),
            'serviceCalls' => ServiceCall::orderByDesc('created_at')->get(),
            'suppliers'    => Supplier::where('status', true)->orderBy('name')->get(),
            'branches'     => Branch::where('status', true)->orderBy('name')->get(),
            'currencies'   => Currency::where('status', true)->orderBy('name')->get(),
            'countries'    => Country::where('status', true)->orderBy('name')->get(),
            'materials'    => Material::where('status', true)->orderBy('name')->get(),
        ];
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'project_id'                 => ['nullable', 'exists:projects,id'],
            'service_call_id'            => ['nullable', 'exists:service_calls,id'],
            'date'                       => ['required', 'date'],
            'supplier_id'                => ['required', 'exists:suppliers,id'],
            'branch_id'                  => ['required', 'exists:branches,id'],
            'shipping_address_line1'     => ['nullable', 'string', 'max:255'],
            'shipping_address_line1_en'  => ['nullable', 'string', 'max:255'],
            'shipping_po_box'            => ['nullable', 'string', 'max:30'],
            'shipping_postal_code'       => ['nullable', 'string', 'max:30'],
            'shipping_city'              => ['nullable', 'string', 'max:100'],
            'shipping_city_en'           => ['nullable', 'string', 'max:100'],
            'shipping_country'           => ['nullable', 'string', 'max:100'],
            'shipping_country_en'        => ['nullable', 'string', 'max:100'],
            'location_scope'             => ['nullable', 'in:inside_jordan,outside_jordan'],
            'governorate'                => ['required_if:location_scope,inside_jordan', 'nullable', 'in:' . implode(',', array_keys(Tender::JORDAN_GOVERNORATES))],
            'country_id'                 => ['required_if:location_scope,outside_jordan', 'nullable', 'exists:countries,id'],
            'currency_id'                => ['nullable', 'exists:currencies,id'],
            'notes'                      => ['nullable', 'string'],
            'items'                      => ['required', 'array', 'min:1'],
            'items.*.material_id'        => ['required', 'exists:materials,id'],
            'items.*.quantity'           => ['required', 'numeric', 'min:0.001'],
            'items.*.unit_price'         => ['required', 'numeric', 'min:0'],
        ]);

        if (($validated['location_scope'] ?? null) === 'outside_jordan') {
            $validated['governorate'] = null;
        } elseif (($validated['location_scope'] ?? null) === 'inside_jordan') {
            $validated['country_id'] = null;
        }

        return $validated;
    }

    private function syncItems(PurchaseRequest $purchaseRequest, array $items): void
    {
        $purchaseRequest->items()->delete();

        foreach ($items as $item) {
            $purchaseRequest->items()->create([
                ...$item,
                'total' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        $purchaseRequest->recalculateTotals();
    }
}
