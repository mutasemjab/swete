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
        $projects     = Project::where('status', 'active')->orderByDesc('created_at')->get();
        $serviceCalls = ServiceCall::orderByDesc('created_at')->get();
        $suppliers    = Supplier::where('status', true)->orderBy('name')->get();
        $branches     = Branch::where('status', true)->orderBy('name')->get();
        $currencies   = Currency::where('status', true)->orderBy('name')->get();
        $countries    = Country::where('status', true)->orderBy('name')->get();
        $materials    = Material::where('status', true)->orderBy('name')->get();

        $project     = $request->filled('project_id') ? Project::find($request->input('project_id')) : null;
        $serviceCall = $request->filled('service_call_id') ? ServiceCall::find($request->input('service_call_id')) : null;

        return $this->moduleView('external-purchases.purchase-requests.create', compact(
            'projects', 'serviceCalls', 'suppliers', 'branches', 'currencies', 'countries', 'materials', 'project', 'serviceCall'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id'           => ['nullable', 'exists:projects,id'],
            'service_call_id'      => ['nullable', 'exists:service_calls,id'],
            'date'                 => ['required', 'date'],
            'supplier_id'          => ['required', 'exists:suppliers,id'],
            'branch_id'            => ['required', 'exists:branches,id'],
            'shipping_address'     => ['nullable', 'string', 'max:255'],
            'location_scope'       => ['nullable', 'in:inside_jordan,outside_jordan'],
            'governorate'          => ['required_if:location_scope,inside_jordan', 'nullable', 'in:' . implode(',', array_keys(Tender::JORDAN_GOVERNORATES))],
            'country_id'           => ['required_if:location_scope,outside_jordan', 'nullable', 'exists:countries,id'],
            'currency_id'          => ['nullable', 'exists:currencies,id'],
            'notes'                => ['nullable', 'string'],
            'items'                => ['required', 'array', 'min:1'],
            'items.*.material_id'  => ['required', 'exists:materials,id'],
            'items.*.quantity'     => ['required', 'numeric', 'min:0.001'],
            'items.*.unit_price'   => ['required', 'numeric', 'min:0'],
        ]);

        if (($validated['location_scope'] ?? null) === 'outside_jordan') {
            $validated['governorate'] = null;
        } elseif (($validated['location_scope'] ?? null) === 'inside_jordan') {
            $validated['country_id'] = null;
        }

        $purchaseRequest = PurchaseRequest::create([
            'number'           => PurchaseRequest::nextNumber(),
            'project_id'       => $validated['project_id'] ?? null,
            'service_call_id'  => $validated['service_call_id'] ?? null,
            'date'             => $validated['date'],
            'supplier_id'      => $validated['supplier_id'],
            'branch_id'        => $validated['branch_id'],
            'shipping_address' => $validated['shipping_address'] ?? null,
            'location_scope'   => $validated['location_scope'] ?? null,
            'governorate'      => $validated['governorate'] ?? null,
            'country_id'       => $validated['country_id'] ?? null,
            'currency_id'      => $validated['currency_id'] ?? null,
            'notes'            => $validated['notes'] ?? null,
            'created_by'       => $request->user()->id,
        ]);

        foreach ($validated['items'] as $item) {
            $purchaseRequest->items()->create([
                ...$item,
                'total' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        $purchaseRequest->recalculateTotals();

        return redirect()->route('purchase-requests.show', $purchaseRequest)
            ->with('success', __('external_purchases.request_added'));
    }

    public function show(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->load(['supplier', 'branch', 'project', 'serviceCall', 'country', 'currency', 'creator', 'items.material.unit']);

        return $this->moduleView('external-purchases.purchase-requests.show', compact('purchaseRequest'));
    }
}
