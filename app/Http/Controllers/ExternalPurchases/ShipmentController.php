<?php

namespace App\Http\Controllers\ExternalPurchases;

use App\Http\Controllers\ModuleController;
use App\Models\Currency;
use App\Models\PurchaseRequest;
use App\Models\Shipment;
use App\Models\ShippingCompany;
use Illuminate\Http\Request;

class ShipmentController extends ModuleController
{
    protected string $module = 'external_purchases';

    public function index(Request $request)
    {
        $query = Shipment::with(['shippingCompany', 'purchaseRequests']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('number', 'like', "%{$search}%");
        }

        $shipments = $query->latest()->paginate(20)->withQueryString();

        return $this->moduleView('external-purchases.shipments.index', compact('shipments'));
    }

    public function create()
    {
        return $this->moduleView('external-purchases.shipments.create', [
            ...$this->formOptions(),
            'shipment' => null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $purchaseRequestIds = $validated['purchase_request_ids'];
        unset($validated['purchase_request_ids']);

        $shipment = Shipment::create([
            ...$validated,
            'number'     => Shipment::nextNumber(),
            'created_by' => $request->user()->id,
        ]);

        $shipment->purchaseRequests()->sync($purchaseRequestIds);
        $this->markLinkedRequestsShipped($shipment);

        return redirect()->route('shipments.show', $shipment)
            ->with('success', __('external_purchases.shipment_added'));
    }

    public function show(Shipment $shipment)
    {
        $shipment->load(['shippingCompany.country', 'currency', 'creator', 'purchaseRequests.supplier']);

        return $this->moduleView('external-purchases.shipments.show', compact('shipment'));
    }

    public function edit(Shipment $shipment)
    {
        $shipment->load('purchaseRequests');

        return $this->moduleView('external-purchases.shipments.edit', [
            ...$this->formOptions($shipment),
            'shipment' => $shipment,
        ]);
    }

    public function update(Request $request, Shipment $shipment)
    {
        $validated = $this->validated($request);
        $purchaseRequestIds = $validated['purchase_request_ids'];
        unset($validated['purchase_request_ids']);

        $shipment->update($validated);
        $shipment->purchaseRequests()->sync($purchaseRequestIds);
        $this->markLinkedRequestsShipped($shipment);

        return redirect()->route('shipments.show', $shipment)
            ->with('success', __('external_purchases.shipment_updated'));
    }

    public function destroy(Shipment $shipment)
    {
        $shipment->delete();

        return redirect()->route('shipments.index')
            ->with('success', __('external_purchases.shipment_deleted'));
    }

    /** Every purchase request attached to this shipment advances to "shipped". */
    private function markLinkedRequestsShipped(Shipment $shipment): void
    {
        foreach ($shipment->purchaseRequests as $purchaseRequest) {
            $purchaseRequest->markShipped();
        }
    }

    /** Eligible = awaiting price quotes — the stage right after the RFQ was sent — plus whatever is already on this shipment when editing. */
    private function formOptions(?Shipment $shipment = null): array
    {
        $eligiblePurchaseRequests = PurchaseRequest::where('status', 'awaiting_price_quotes')
            ->orWhereIn('id', $shipment?->purchaseRequests->pluck('id') ?? [])
            ->with('supplier')->orderByDesc('date')->get();

        return [
            'eligiblePurchaseRequests' => $eligiblePurchaseRequests,
            'shippingCompanies'        => ShippingCompany::where('status', true)->orderBy('name')->get(),
            'currencies'               => Currency::where('status', true)->orderBy('name')->get(),
        ];
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'purchase_request_ids'   => ['required', 'array', 'min:1'],
            'purchase_request_ids.*' => ['exists:purchase_requests,id'],
            'shipping_company_id'    => ['required', 'exists:shipping_companies,id'],
            'transport_mode'         => ['required', 'in:' . implode(',', Shipment::TRANSPORT_MODES)],
            'sea_service_type'       => ['required_if:transport_mode,sea', 'nullable', 'in:' . implode(',', Shipment::SEA_SERVICE_TYPES)],
            'air_service_type'       => ['required_if:transport_mode,air', 'nullable', 'in:' . implode(',', Shipment::AIR_SERVICE_TYPES)],
            'incoterm'                => ['nullable', 'in:' . implode(',', Shipment::INCOTERMS)],
            'price'                   => ['nullable', 'numeric', 'min:0'],
            'currency_id'             => ['nullable', 'exists:currencies,id'],
            'is_hazardous'            => ['boolean'],
            'shipping_line'           => ['nullable', 'string', 'max:150'],
            'bill_of_lading_number'   => ['nullable', 'string', 'max:100'],
            'container_number'        => ['nullable', 'string', 'max:100'],
            'notes'                   => ['nullable', 'string'],
        ]);

        if ($validated['transport_mode'] !== 'sea') {
            $validated['sea_service_type'] = null;
        }

        if ($validated['transport_mode'] !== 'air') {
            $validated['air_service_type'] = null;
        }

        $validated['is_hazardous'] = $request->boolean('is_hazardous');

        return $validated;
    }
}
