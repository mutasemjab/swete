<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\ModuleController;
use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseOrder;
use App\Models\StockVoucher;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Support\BackNavigation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseInvoiceController extends ModuleController
{
    protected string $module = 'warehouse';

    public function index(Request $request)
    {
        $invoices = PurchaseInvoice::with(['supplier', 'warehouse'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return $this->moduleView('warehouse.purchase-invoices.index', compact('invoices'));
    }

    public function create(Request $request)
    {
        // Reached from a Purchase Order's own "Convert to Purchase Invoice" button (?purchase_order_id=X)
        // — items/supplier/warehouse carry straight over, never re-typed. The PO must still be a
        // draft (not already converted by some other invoice).
        $purchaseOrder = null;
        if ($request->filled('purchase_order_id')) {
            $purchaseOrder = PurchaseOrder::with('items')->findOrFail($request->input('purchase_order_id'));
            abort_unless($purchaseOrder->isDraft(), 404);
        }

        return $this->moduleView('warehouse.purchase-invoices.create', [
            'purchaseOrder' => $purchaseOrder,
            ...$this->formOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id'             => ['required', 'exists:suppliers,id'],
            'supplier_invoice_number' => ['required', 'string', 'max:100'],
            'warehouse_id'            => ['required', 'exists:warehouses,id'],
            'purchase_order_id'       => ['nullable', 'exists:purchase_orders,id'],
            'date'                    => ['required', 'date'],
            'notes'                   => ['nullable', 'string'],
            'items'                   => ['required', 'array', 'min:1'],
            'items.*.material_id'     => ['required', 'exists:materials,id'],
            'items.*.quantity'        => ['required', 'numeric', 'min:0.001'],
            'items.*.unit_price'      => ['required', 'numeric', 'min:0'],
            'items.*.notes'           => ['nullable', 'string', 'max:255'],
        ]);

        $invoice = DB::transaction(function () use ($validated, $request) {
            $subtotal = collect($validated['items'])->sum(fn ($i) => $i['quantity'] * $i['unit_price']);

            $invoice = PurchaseInvoice::create([
                'number'                  => PurchaseInvoice::nextNumber(),
                'supplier_id'             => $validated['supplier_id'],
                'supplier_invoice_number' => $validated['supplier_invoice_number'],
                'warehouse_id'            => $validated['warehouse_id'],
                'purchase_order_id'       => $validated['purchase_order_id'] ?? null,
                'date'                    => $validated['date'],
                'notes'                   => $validated['notes'] ?? null,
                'subtotal'                => $subtotal,
                'total'                   => $subtotal,
                'created_by'              => $request->user()->id,
            ]);

            foreach ($validated['items'] as $item) {
                $invoice->items()->create([
                    'material_id' => $item['material_id'],
                    'quantity'    => $item['quantity'],
                    'unit_price'  => $item['unit_price'],
                    'total'       => $item['quantity'] * $item['unit_price'],
                    'notes'       => $item['notes'] ?? null,
                ]);
            }

            if (! empty($validated['purchase_order_id'])) {
                $purchaseOrder = PurchaseOrder::findOrFail($validated['purchase_order_id']);
                $purchaseOrder->update(['status' => 'converted', 'purchase_invoice_id' => $invoice->id]);
            }

            // The invoice carries price; the receipt voucher it auto-generates never does — the
            // physical stock-in only ever needs quantities (see StockVoucher's own doc comment).
            $voucher = StockVoucher::create([
                'type'         => 'receipt',
                'number'       => StockVoucher::nextNumber('receipt'),
                'warehouse_id' => $invoice->warehouse_id,
                'reference_no' => $invoice->number,
                'notes'        => __('warehouse.purchase_invoice_voucher_notes', ['number' => $invoice->number]),
                'date'         => $invoice->date,
                'status'       => 'draft',
                'created_by'   => $request->user()->id,
            ]);

            foreach ($invoice->items as $item) {
                $voucher->items()->create([
                    'material_id' => $item->material_id,
                    'quantity'    => $item->quantity,
                ]);
            }

            $voucher->post($request->user());
            $invoice->update(['stock_voucher_id' => $voucher->id]);

            return $invoice;
        });

        return redirect()->route('purchase-invoices.show', [$invoice, 'back' => $request->input('back')])
            ->with('success', __('warehouse.purchase_invoice_added'));
    }

    public function show(Request $request, PurchaseInvoice $purchaseInvoice)
    {
        $purchaseInvoice->load(['supplier', 'warehouse', 'purchaseOrder', 'stockVoucher', 'creator', 'items.material.unit']);

        $backUrl = BackNavigation::url();

        return $this->moduleView('warehouse.purchase-invoices.show', ['invoice' => $purchaseInvoice, 'backUrl' => $backUrl]);
    }

    private function formOptions(): array
    {
        return [
            'suppliers'          => Supplier::where('status', true)->orderBy('name')->get(),
            'warehouses'         => Warehouse::where('status', true)->orderBy('name')->get(),
            'materials'          => Material::confirmed()->where('status', true)->orderBy('name')->get(),
            'materialCategories' => MaterialCategory::orderBy('name')->get(),
            'units'              => Unit::where('status', true)->orderBy('name')->get(),
        ];
    }
}
