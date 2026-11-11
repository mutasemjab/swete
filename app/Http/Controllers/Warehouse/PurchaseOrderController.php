<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\ModuleController;
use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Support\BackNavigation;
use Illuminate\Http\Request;

class PurchaseOrderController extends ModuleController
{
    protected string $module = 'warehouse';

    public function index(Request $request)
    {
        $orders = PurchaseOrder::with(['supplier', 'warehouse'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return $this->moduleView('warehouse.purchase-orders.index', compact('orders'));
    }

    public function create()
    {
        return $this->moduleView('warehouse.purchase-orders.create', [
            'order' => null,
            ...$this->formOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        $order = PurchaseOrder::create([
            'number'       => PurchaseOrder::nextNumber(),
            'supplier_id'  => $validated['supplier_id'],
            'warehouse_id' => $validated['warehouse_id'],
            'date'         => $validated['date'],
            'notes'        => $validated['notes'] ?? null,
            'status'       => 'draft',
            'created_by'   => $request->user()->id,
        ]);

        $this->syncItems($order, $validated['items']);

        return redirect()->route('purchase-orders.show', [$order, 'back' => $request->input('back')])
            ->with('success', __('warehouse.purchase_order_added'));
    }

    public function show(Request $request, PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'warehouse', 'creator', 'items.material.unit', 'invoice']);

        $backUrl = BackNavigation::url();

        return $this->moduleView('warehouse.purchase-orders.show', ['order' => $purchaseOrder, 'backUrl' => $backUrl]);
    }

    public function edit(Request $request, PurchaseOrder $purchaseOrder)
    {
        abort_unless($purchaseOrder->isDraft(), 404);
        $purchaseOrder->load('items');

        $backUrl = BackNavigation::url([$purchaseOrder], 'purchase-orders.show');

        return $this->moduleView('warehouse.purchase-orders.edit', [
            'order'   => $purchaseOrder,
            'backUrl' => $backUrl,
            ...$this->formOptions(),
        ]);
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        abort_unless($purchaseOrder->isDraft(), 404);

        $validated = $this->validated($request);

        $purchaseOrder->update([
            'supplier_id'  => $validated['supplier_id'],
            'warehouse_id' => $validated['warehouse_id'],
            'date'         => $validated['date'],
            'notes'        => $validated['notes'] ?? null,
        ]);

        $this->syncItems($purchaseOrder, $validated['items']);

        return redirect()->route('purchase-orders.show', [$purchaseOrder, 'back' => $request->input('back')])
            ->with('success', __('warehouse.purchase_order_updated'));
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

    private function validated(Request $request): array
    {
        return $request->validate([
            'supplier_id'          => ['required', 'exists:suppliers,id'],
            'warehouse_id'         => ['required', 'exists:warehouses,id'],
            'date'                 => ['required', 'date'],
            'notes'                => ['nullable', 'string'],
            'items'                => ['required', 'array', 'min:1'],
            'items.*.material_id'  => ['required', 'exists:materials,id'],
            'items.*.quantity'     => ['required', 'numeric', 'min:0.001'],
            'items.*.unit_price'   => ['required', 'numeric', 'min:0'],
            'items.*.notes'        => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function syncItems(PurchaseOrder $order, array $items): void
    {
        $order->items()->delete();

        foreach ($items as $item) {
            $order->items()->create([
                'material_id' => $item['material_id'],
                'quantity'    => $item['quantity'],
                'unit_price'  => $item['unit_price'],
                'total'       => $item['quantity'] * $item['unit_price'],
                'notes'       => $item['notes'] ?? null,
            ]);
        }

        $order->recalculateTotals();
    }
}
