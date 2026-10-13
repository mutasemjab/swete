<?php

namespace App\Http\Controllers\Tenders;

use App\Http\Controllers\ModuleController;
use App\Models\Branch;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceType;
use App\Models\MaintenanceReport;
use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\MaterialStock;
use App\Models\PriceAnalysis;
use App\Models\PriceQuote;
use App\Models\PriceQuoteInstallment;
use App\Models\PriceQuoteItem;
use App\Models\QuoteDeliveryTerm;
use App\Models\QuoteDiscountLimit;
use App\Models\QuoteSupplyScope;
use App\Models\Tender;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PriceQuoteController extends ModuleController
{
    // Price Quotes is now its own standalone module (see config/modules.php's 'quotations' key) —
    // no longer shared/linked from the Tenders or Maintenance sidebars, so this just reports its
    // own module like every other controller. (A session-based multi-module trick used to live
    // here when Price Quotes really was reachable from two different sidebars; that's no longer
    // the case, so the simpler static value is correct again.)
    protected string $module = 'quotations';

    public function index(Request $request)
    {
        // Archived/old versions don't clutter the main list — see PriceQuote::archiveCurrentVersion().
        $query = PriceQuote::with(['customer', 'tender', 'assignee', 'invoice'])->where('is_current', true);

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->input('assigned_to'));
        }

        $priceQuotes = $query->latest()->paginate(20)->withQueryString();

        return $this->moduleView('tenders.price-quotes.index', [
            'priceQuotes' => $priceQuotes,
            'customers'   => Customer::where('status', true)->orderBy('name')->get(),
            'employees'   => User::where('status', true)->orderBy('name')->get(),
        ]);
    }

    public function create(Request $request)
    {
        $tender = $request->filled('tender_id') ? Tender::find($request->input('tender_id')) : null;
        $report = $request->filled('report_id')
            ? MaintenanceReport::with('materials.material')->find($request->input('report_id'))
            : null;

        return $this->moduleView('tenders.price-quotes.create', [
            ...$this->formOptions(),
            ...$this->historyData(),
            'priceQuote'        => null,
            'tender'            => $tender,
            'report'            => $report,
            'seedInstallments'  => [['description' => '', 'amount' => '', 'due_date' => '']],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['report_id' => ['nullable', 'exists:maintenance_reports,id']]);

        $validated = $this->validated($request);

        $priceQuote = PriceQuote::create([
            ...$validated,
            'number'     => PriceQuote::nextNumber(),
            'status'     => 'draft',
            'created_by' => $request->user()->id,
        ]);

        $this->syncItems($priceQuote, $validated['items']);
        $this->syncInstallments($priceQuote, $validated['installments'] ?? []);

        if ($request->filled('report_id')) {
            MaintenanceReport::where('id', $request->input('report_id'))->update(['price_quote_id' => $priceQuote->id]);
        }

        return redirect()->route('price-quotes.show', $priceQuote)
            ->with('success', __('tenders.quote_added'));
    }

    public function show(PriceQuote $priceQuote)
    {
        $priceQuote->load([
            'customer', 'tender', 'creator', 'branch', 'currency', 'supplyScope', 'deliveryTerm',
            'items.material.unit', 'assignee', 'invoice', 'installments', 'versions', 'root',
        ]);

        return $this->moduleView('tenders.price-quotes.show', compact('priceQuote'));
    }

    public function edit(PriceQuote $priceQuote)
    {
        // A frozen historical version is read-only — only the live quote can be edited.
        abort_unless($priceQuote->is_current, 404);

        $priceQuote->load(['items', 'installments']);

        $seedInstallments = $priceQuote->installments->map(fn ($i) => [
            'description' => $i->description ?? '',
            'amount'      => (string) $i->amount,
            'due_date'    => $i->due_date?->format('Y-m-d') ?? '',
        ])->toArray();

        if (empty($seedInstallments)) {
            $seedInstallments = [['description' => '', 'amount' => '', 'due_date' => '']];
        }

        return $this->moduleView('tenders.price-quotes.edit', [
            ...$this->formOptions(),
            ...$this->historyData($priceQuote),
            'priceQuote'       => $priceQuote,
            'tender'           => $priceQuote->tender,
            'seedInstallments' => $seedInstallments,
        ]);
    }

    public function update(Request $request, PriceQuote $priceQuote)
    {
        abort_unless($priceQuote->is_current, 404);

        $validated = $this->validated($request);

        // Freeze the pre-edit state into a new read-only row before touching anything — the live
        // row (this one) keeps its id/number and just moves forward to the next version number.
        $priceQuote->archiveCurrentVersion();

        $validated['version'] = $priceQuote->version + 1;
        $priceQuote->update($validated);

        $this->syncItems($priceQuote, $validated['items']);
        $this->syncInstallments($priceQuote, $validated['installments'] ?? []);

        return redirect()->route('price-quotes.show', $priceQuote)
            ->with('success', __('tenders.quote_updated'));
    }

    /**
     * Standalone, print-optimized 2-page A4 document — deliberately not wrapped in the app shell.
     * `?lang=ar|en` prints in that language regardless of the viewer's own session locale (scoped
     * to this one request/render only — never persisted, never affects anything else).
     */
    public function printDocument(Request $request, PriceQuote $priceQuote)
    {
        if (in_array($request->query('lang'), ['ar', 'en'], true)) {
            app()->setLocale($request->query('lang'));
        }

        $priceQuote->load([
            'branch', 'currency', 'customer', 'tender', 'supplyScope', 'deliveryTerm',
            'items.material.unit', 'installments',
        ]);

        return view('tenders.price-quotes.print', compact('priceQuote'));
    }

    /** Bulk-hand a batch of quotes to one employee, who will see them as pending conversion. */
    public function assign(Request $request)
    {
        $validated = $request->validate([
            'quote_ids'   => ['required', 'array', 'min:1'],
            'quote_ids.*' => ['exists:price_quotes,id'],
            'assigned_to' => ['required', 'exists:users,id'],
        ]);

        // Defensive re-filter: a stale checkbox selection shouldn't reassign an already-invoiced quote.
        $updated = PriceQuote::whereIn('id', $validated['quote_ids'])
            ->whereNull('invoice_id')
            ->where('is_current', true)
            ->update(['assigned_to' => $validated['assigned_to']]);

        return back()->with('success', __('tenders.quote_sent_to_employee', ['count' => $updated]));
    }

    /** One-click: turn this quote into a draft sales invoice. Only the employee it was sent to may do this. */
    public function convertToInvoice(Request $request, PriceQuote $priceQuote)
    {
        abort_unless($priceQuote->is_current, 404);
        abort_unless($priceQuote->assigned_to === $request->user()->id, 403, __('tenders.quote_convert_unauthorized'));

        if ($priceQuote->invoice_id) {
            return back()->with('error', __('tenders.quote_already_converted'));
        }

        $priceQuote->load('items.material');

        $type = InvoiceType::where('party_type', 'customer')->where('status', true)->first();

        if (! $type) {
            return back()->with('error', __('tenders.quote_no_invoice_type'));
        }

        $invoice = Invoice::create([
            'invoice_type_id' => $type->id,
            'party_type'      => 'customer',
            'party_id'        => $priceQuote->customer_id,
            'number'          => Invoice::nextNumber($type),
            'date'            => now()->toDateString(),
            'currency_id'     => $priceQuote->currency_id,
            'notes'           => __('tenders.quote_convert_invoice_note', ['number' => $priceQuote->number]),
            'status'          => 'draft',
            'created_by'      => $request->user()->id,
        ]);

        foreach ($priceQuote->items as $item) {
            $description = $item->material?->localized_name ?? '';

            if ($item->notes) {
                $description .= ' (' . implode(', ', $item->notes) . ')';
            }

            $invoice->items()->create([
                'description' => $description,
                'quantity'    => $item->quantity,
                'unit_price'  => $item->unit_price,
                'total'       => $item->quantity * $item->unit_price,
            ]);
        }

        if ($priceQuote->discount_amount > 0) {
            $invoice->items()->create([
                'description' => __('tenders.quote_discount'),
                'quantity'    => 1,
                'unit_price'  => -$priceQuote->discount_amount,
                'total'       => -$priceQuote->discount_amount,
            ]);
        }

        $invoice->recalculateTotals();

        $priceQuote->update(['invoice_id' => $invoice->id]);

        return redirect()->route('accounting.invoices.show', $invoice)
            ->with('success', __('tenders.quote_converted'));
    }

    private function formOptions(): array
    {
        return [
            'customers'      => Customer::where('status', true)->orderBy('name')->get(),
            // Deliberately NOT ->confirmed() here — a quote must still show its own draft
            // materials (quick-added before the tender was won) in the picker.
            'materials'      => Material::where('status', true)->orderBy('name')->get(),
            'branches'       => Branch::where('status', true)->orderBy('name')->get(),
            'currencies'     => Currency::where('status', true)->orderBy('name')->get(),
            'supplyScopes'   => QuoteSupplyScope::where('status', true)->orderBy('name')->get(),
            'deliveryTerms'  => QuoteDeliveryTerm::where('status', true)->orderBy('name')->get(),
            // For the "quick add material" modal on the items table.
            'materialCategories' => MaterialCategory::orderBy('name')->get(),
            'units'              => Unit::where('status', true)->orderBy('name')->get(),
            'discountLimit'      => QuoteDiscountLimit::current(),
        ];
    }

    /**
     * What the create/edit form needs to answer, live, while the user is picking a customer or a
     * material: quotes previously sent to this customer, this material's unit price on other
     * quotes, and how much of it is currently on hand across all warehouses.
     */
    private function historyData(?PriceQuote $current = null): array
    {
        $quotesQuery = PriceQuote::with('currency')->orderByDesc('date');

        if ($current) {
            $quotesQuery->where('id', '!=', $current->id);
        }

        $customerQuoteHistory = $quotesQuery->get(['id', 'customer_id', 'number', 'date', 'total', 'currency_id'])
            ->groupBy('customer_id')
            ->map(fn ($quotes) => $quotes->take(8)->map(fn ($quote) => [
                'number'   => $quote->number,
                'date'     => $quote->date->format('Y-m-d'),
                'total'    => (float) $quote->total,
                'currency' => $quote->currency?->code,
                'url'      => route('price-quotes.show', $quote->id),
            ])->values());

        $itemsQuery = PriceQuoteItem::query()->with('priceQuote:id,number,date')->latest('id');

        if ($current) {
            $itemsQuery->where('price_quote_id', '!=', $current->id);
        }

        $materialPriceHistory = $itemsQuery->get(['id', 'price_quote_id', 'material_id', 'quantity', 'unit_price'])
            ->filter(fn ($item) => $item->priceQuote !== null)
            ->groupBy('material_id')
            ->map(fn ($items) => $items->sortByDesc(fn ($item) => $item->priceQuote->date)->take(8)->map(fn ($item) => [
                'number'     => $item->priceQuote->number,
                'date'       => $item->priceQuote->date->format('Y-m-d'),
                'quantity'   => (float) $item->quantity,
                'unit_price' => (float) $item->unit_price,
            ])->values());

        $materialStock = MaterialStock::selectRaw('material_id, SUM(quantity) as qty')
            ->groupBy('material_id')
            ->pluck('qty', 'material_id');

        // So a quote item can be added straight from a saved Price Analysis line: its per-unit price
        // is the analysis line's final total (To JD + shipping) divided back out over its quantity,
        // since the analysis only ever multiplies quantity into the profit figure, not the totals.
        $priceAnalyses = PriceAnalysis::with('items.material')->latest()->take(50)->get()
            ->map(fn ($analysis) => [
                'id'     => $analysis->id,
                'number' => $analysis->number,
                'items'  => $analysis->items->map(fn ($item) => [
                    'material_id'   => $item->material_id,
                    'material_name' => $item->material?->localized_name,
                    'ciat_model'    => $item->ciat_model,
                    'quantity'      => (float) $item->quantity,
                    'unit_price'    => (float) $item->quantity > 0
                        ? round(((float) $item->to_jd + (float) $item->shipping) / (float) $item->quantity, 3)
                        : (float) $item->to_jd + (float) $item->shipping,
                ])->values(),
            ])->values();

        return [
            'customerQuoteHistory' => $customerQuoteHistory,
            'materialPriceHistory' => $materialPriceHistory,
            'materialStock'        => $materialStock,
            'priceAnalyses'        => $priceAnalyses,
        ];
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'tender_id'                => ['nullable', 'exists:tenders,id'],
            'customer_id'               => ['required', 'exists:customers,id'],
            'date'                      => ['required', 'date'],
            'branch_id'                 => ['required', 'exists:branches,id'],
            'currency_id'               => ['required', 'exists:currencies,id'],
            'validity_weeks'            => ['nullable', 'integer', 'min:1'],
            'supply_scope_id'           => ['nullable', 'exists:quote_supply_scopes,id'],
            'delivery_term_id'          => ['nullable', 'exists:quote_delivery_terms,id'],
            'winching_included'         => ['boolean'],
            'sales_tax_included'        => ['boolean'],
            'customs_fees_included'     => ['boolean'],
            'include_boiler_note'       => ['boolean'],
            'additional_terms'          => ['nullable', 'string'],
            'notes'                     => ['nullable', 'string'],
            'items'                     => ['required', 'array', 'min:1'],
            'items.*.material_id'       => ['required', 'exists:materials,id'],
            'items.*.quantity'          => ['required', 'numeric', 'min:0.001'],
            'items.*.unit_price'        => ['required', 'numeric', 'min:0'],
            'items.*.notes'             => ['nullable', 'array'],
            'items.*.notes.*'           => ['nullable', 'string', 'max:500'],
            'installments'              => ['nullable', 'array'],
            'installments.*.description'=> ['nullable', 'string', 'max:255'],
            'installments.*.amount'     => ['required_with:installments.*', 'nullable', 'numeric', 'min:0'],
            'installments.*.due_date'   => ['nullable', 'date'],
            'discount_type'             => ['required', 'in:amount,percent'],
            'discount_value'            => ['nullable', 'numeric', 'min:0', $request->input('discount_type') === 'percent' ? 'max:100' : 'max:999999999'],
        ]);

        $validated['discount_value'] = (float) ($validated['discount_value'] ?? 0);

        $subtotal = collect($validated['items'])->sum(fn ($item) => $item['quantity'] * $item['unit_price']);

        if ($validated['discount_type'] === 'amount' && $validated['discount_value'] > $subtotal) {
            throw ValidationException::withMessages(['discount_value' => __('tenders.quote_discount_exceeds')]);
        }

        $limit = QuoteDiscountLimit::current();

        if ($validated['discount_type'] === 'percent' && $limit->max_discount_percent !== null
            && $validated['discount_value'] > (float) $limit->max_discount_percent) {
            throw ValidationException::withMessages(['discount_value' => __('tenders.quote_discount_over_limit_percent', [
                'limit' => (float) $limit->max_discount_percent,
            ])]);
        }

        if ($validated['discount_type'] === 'amount' && $limit->max_discount_amount !== null
            && $validated['discount_value'] > (float) $limit->max_discount_amount) {
            throw ValidationException::withMessages(['discount_value' => __('tenders.quote_discount_over_limit_amount', [
                'limit' => (float) $limit->max_discount_amount,
            ])]);
        }

        $validated['winching_included']     = $request->boolean('winching_included');
        $validated['sales_tax_included']    = $request->boolean('sales_tax_included');
        $validated['customs_fees_included'] = $request->boolean('customs_fees_included');
        $validated['include_boiler_note']   = $request->boolean('include_boiler_note');

        return $validated;
    }

    private function syncItems(PriceQuote $priceQuote, array $items): void
    {
        $priceQuote->items()->delete();

        foreach ($items as $item) {
            $notes = array_values(array_filter(array_map(fn ($note) => trim((string) $note), $item['notes'] ?? []), fn ($note) => $note !== ''));

            $priceQuote->items()->create([
                ...$item,
                'notes' => $notes ?: null,
                'total' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        $priceQuote->recalculateTotals();
    }

    private function syncInstallments(PriceQuote $priceQuote, array $installments): void
    {
        $priceQuote->installments()->delete();

        $sort = 0;
        foreach ($installments as $row) {
            $amount = trim((string) ($row['amount'] ?? ''));
            if ($amount === '' || $amount === null) {
                continue;
            }

            $priceQuote->installments()->create([
                'description' => $row['description'] ?? null,
                'amount'      => $amount,
                'due_date'    => $row['due_date'] ?: null,
                'sort_order'  => $sort++,
            ]);
        }
    }
}
