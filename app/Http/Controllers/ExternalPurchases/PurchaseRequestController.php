<?php

namespace App\Http\Controllers\ExternalPurchases;

use App\Http\Controllers\ModuleController;
use App\Mail\ShippingQuoteRequestMail;
use App\Mail\VendorPurchaseOrderMail;
use App\Models\Branch;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Material;
use App\Models\Project;
use App\Models\PurchaseRequest;
use App\Models\ServiceCall;
use App\Models\ShippingCompany;
use App\Models\Supplier;
use App\Models\Tender;
use App\Models\VendorEmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
        $this->syncAdditionalNotes($purchaseRequest, $validated['additional_notes'] ?? []);
        $purchaseRequest->seedApprovals();

        return redirect()->route('purchase-requests.show', $purchaseRequest)
            ->with('success', __('external_purchases.request_added'));
    }

    public function show(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->load([
            'supplier', 'branch', 'project', 'serviceCall', 'country', 'currency', 'creator',
            'items.material.unit', 'items.features',
            'approvals.user', 'attachments.creator', 'additionalNotes',
        ]);

        $emailTemplate = $purchaseRequest->status === 'approved'
            ? VendorEmailTemplate::current()->forNumber($purchaseRequest->number)
            : null;

        return $this->moduleView('external-purchases.purchase-requests.show', compact('purchaseRequest', 'emailTemplate'));
    }

    public function edit(PurchaseRequest $purchaseRequest)
    {
        abort_unless($purchaseRequest->isEditable(), 403, __('external_purchases.request_locked'));

        $purchaseRequest->load('items.features', 'additionalNotes');

        return $this->moduleView('external-purchases.purchase-requests.edit', [
            ...$this->formOptions(),
            'purchaseRequest' => $purchaseRequest,
            'project'         => $purchaseRequest->project,
            'serviceCall'     => $purchaseRequest->serviceCall,
        ]);
    }

    public function update(Request $request, PurchaseRequest $purchaseRequest)
    {
        abort_unless($purchaseRequest->isEditable(), 403, __('external_purchases.request_locked'));

        $validated = $this->validated($request);

        $purchaseRequest->update($validated);

        $this->syncItems($purchaseRequest, $validated['items']);
        $this->syncAdditionalNotes($purchaseRequest, $validated['additional_notes'] ?? []);

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
        $purchaseRequest->load(['supplier', 'branch', 'project', 'serviceCall', 'country', 'currency', 'creator', 'items.material.unit', 'additionalNotes']);

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
            'items.*.ercd'               => ['nullable', 'string', 'max:150'],
            'items.*.unit_price'         => ['required', 'numeric', 'min:0'],
            'items.*.features'           => ['array'],
            'items.*.features.*'         => ['nullable', 'string', 'max:255'],
            'additional_notes'           => ['array'],
            'additional_notes.*.label'   => ['required_with:additional_notes', 'string', 'max:150'],
            'additional_notes.*.value'   => ['nullable', 'string', 'max:255'],
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
            $features = $item['features'] ?? [];
            unset($item['features']);

            $createdItem = $purchaseRequest->items()->create([
                ...$item,
                'total' => $item['quantity'] * $item['unit_price'],
            ]);

            foreach (array_filter($features, fn ($value) => filled($value)) as $value) {
                $createdItem->features()->create(['value' => $value]);
            }
        }

        $purchaseRequest->recalculateTotals();
    }

    private function syncAdditionalNotes(PurchaseRequest $purchaseRequest, array $rows): void
    {
        $purchaseRequest->additionalNotes()->delete();

        foreach ($rows as $row) {
            $purchaseRequest->additionalNotes()->create([
                'label' => $row['label'],
                'value' => $row['value'] ?? null,
            ]);
        }
    }

    /** Approve/reject as the current user, if they have a still-pending approval row. */
    public function approve(Request $request, PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->recordDecision($request->user(), 'approved');

        return back()->with('success', __('external_purchases.approval_recorded'));
    }

    public function reject(Request $request, PurchaseRequest $purchaseRequest)
    {
        $validated = $request->validate(['note' => ['nullable', 'string', 'max:500']]);

        $purchaseRequest->recordDecision($request->user(), 'rejected', $validated['note'] ?? null);

        return back()->with('success', __('external_purchases.approval_recorded'));
    }

    public function markSent(Request $request, PurchaseRequest $purchaseRequest)
    {
        if ($purchaseRequest->status !== 'approved') {
            return back()->with('error', __('external_purchases.mark_sent_invalid'));
        }

        $validated = $request->validate([
            'email_subject' => ['required', 'string', 'max:255'],
            'email_body'    => ['required', 'string'],
        ]);

        if (! $purchaseRequest->supplier?->email) {
            return back()->with('error', __('external_purchases.supplier_email_missing'));
        }

        Mail::to($purchaseRequest->supplier->email)
            ->send(new VendorPurchaseOrderMail($validated['email_subject'], $validated['email_body']));

        $purchaseRequest->markSent();

        return back()->with('success', __('external_purchases.request_marked_sent'));
    }

    public function updateManufacturing(Request $request, PurchaseRequest $purchaseRequest)
    {
        $validated = $request->validate([
            'so_number'  => ['required', 'string', 'max:100'],
            'ready_date' => ['required', 'date'],
        ]);

        $purchaseRequest->updateManufacturingInfo($validated['so_number'], $validated['ready_date']);

        return back()->with('success', __('external_purchases.manufacturing_updated'));
    }

    /** Eligible = still in manufacturing — once shipped, status moves on and it drops off this list. */
    public function shipmentForm(Request $request)
    {
        $eligiblePurchaseRequests = PurchaseRequest::where('status', 'manufacturing')
            ->with('supplier')->orderByDesc('date')->get();

        $selectedIds = collect($request->query('purchase_request_ids', []))->map(fn ($id) => (int) $id);

        $shippingCompanies = ShippingCompany::where('status', true)->orderBy('name')->get();

        return $this->moduleView('external-purchases.purchase-requests.ship', compact(
            'eligiblePurchaseRequests', 'selectedIds', 'shippingCompanies'
        ));
    }

    public function sendToShippingCompanies(Request $request)
    {
        $validated = $request->validate([
            'purchase_request_ids'   => ['required', 'array', 'min:1'],
            'purchase_request_ids.*' => ['exists:purchase_requests,id'],
            'shipping_company_ids'   => ['required', 'array', 'min:1'],
            'shipping_company_ids.*' => ['exists:shipping_companies,id'],
            'message'                => ['nullable', 'string', 'max:2000'],
            'attachments'            => ['nullable', 'array'],
            'attachments.*'          => ['file', 'max:10240'],
        ]);

        // Re-filter by status defensively — a stale form shouldn't ship a PR that's since moved on.
        $purchaseRequests = PurchaseRequest::whereIn('id', $validated['purchase_request_ids'])
            ->where('status', 'manufacturing')->get();
        $companies = ShippingCompany::whereIn('id', $validated['shipping_company_ids'])->get();
        $files     = $request->file('attachments', []);

        foreach ($companies as $company) {
            Mail::to($company->email)->send(new ShippingQuoteRequestMail(
                $purchaseRequests, $company, $validated['message'] ?? null, $files
            ));

            foreach ($purchaseRequests as $purchaseRequest) {
                $purchaseRequest->shippingRequests()->create([
                    'shipping_company_id' => $company->id,
                    'sent_by'             => $request->user()->id,
                    'sent_at'             => now(),
                ]);
            }
        }

        foreach ($purchaseRequests as $purchaseRequest) {
            activity()
                ->performedOn($purchaseRequest)
                ->causedBy($request->user())
                ->withProperties(['shipping_companies' => $companies->pluck('name')])
                ->log('shipping_rfq_sent');

            $purchaseRequest->markAwaitingPriceQuotes();
        }

        return redirect()->route('purchase-requests.index')
            ->with('success', __('external_purchases.shipping_rfq_sent'));
    }
}
