<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\ModuleController;
use App\Models\Customer;
use App\Models\Material;
use App\Models\MaterialStock;
use App\Models\StockVoucher;
use App\Models\Warehouse;
use App\Support\BackNavigation;
use Illuminate\Http\Request;

class StockVoucherController extends ModuleController
{
    protected string $module = 'warehouse';

    private const TYPES = ['receipt', 'issue', 'transfer', 'stocktake'];

    public function index(Request $request, string $type)
    {
        $this->assertType($type);

        $vouchers = StockVoucher::with(['warehouse', 'destinationWarehouse', 'creator'])
            ->where('type', $type)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return $this->moduleView('warehouse.vouchers.index', compact('vouchers', 'type'));
    }

    public function create(string $type)
    {
        $this->assertType($type);

        $warehouses = Warehouse::where('status', true)->orderBy('name')->get();
        $materials  = Material::confirmed()->where('status', true)->orderBy('name')->get();
        $customers  = $type === 'issue' ? Customer::where('status', true)->orderBy('name')->get() : collect();
        // Every (material, warehouse) current balance, flat — the warehouse isn't picked yet at
        // this point, so the stocktake form looks this up client-side once the user picks one,
        // keyed as `${materialId}-${warehouseId}` (see create.blade.php).
        $materialStocks = $type === 'stocktake'
            ? MaterialStock::select('material_id', 'warehouse_id', 'quantity')->get()
                ->mapWithKeys(fn ($s) => [$s->material_id . '-' . $s->warehouse_id => (float) $s->quantity])
            : collect();

        return $this->moduleView('warehouse.vouchers.create', compact('type', 'warehouses', 'materials', 'customers', 'materialStocks'));
    }

    public function store(Request $request, string $type)
    {
        $this->assertType($type);

        $rules = [
            'warehouse_id'             => ['required', 'exists:warehouses,id'],
            'destination_warehouse_id' => [$type === 'transfer' ? 'required' : 'nullable', 'exists:warehouses,id', 'different:warehouse_id'],
            'customer_id'              => ['nullable', 'exists:customers,id'],
            'date'                     => ['required', 'date'],
            'reference_no'             => ['nullable', 'string', 'max:100'],
            'notes'                    => ['nullable', 'string'],
            'items'                    => ['required', 'array', 'min:1'],
            'items.*.material_id'      => ['required', 'exists:materials,id'],
            // A stocktake's "quantity" is the physically counted amount — 0 is a perfectly valid
            // count (an item fully depleted), unlike every other type where a zero-quantity line
            // is meaningless and almost certainly a mistake.
            'items.*.quantity'         => ['required', 'numeric', $type === 'stocktake' ? 'min:0' : 'min:0.001'],
            'items.*.unit_cost'        => ['nullable', 'numeric', 'min:0'],
            'items.*.notes'           => ['nullable', 'string', 'max:255'],
        ];

        $validated = $request->validate($rules);

        $voucher = StockVoucher::create([
            'type'                     => $type,
            'number'                   => StockVoucher::nextNumber($type),
            'warehouse_id'             => $validated['warehouse_id'],
            'destination_warehouse_id' => $validated['destination_warehouse_id'] ?? null,
            'customer_id'              => $validated['customer_id'] ?? null,
            'date'                     => $validated['date'],
            'reference_no'             => $validated['reference_no'] ?? null,
            'notes'                    => $validated['notes'] ?? null,
            'status'                   => 'draft',
            'created_by'               => $request->user()->id,
        ]);

        foreach ($validated['items'] as $item) {
            $voucher->items()->create($item);
        }

        return redirect()->route('warehouse.vouchers.show', ['type' => $type, 'voucher' => $voucher, 'back' => $request->input('back')])
            ->with('success', __('warehouse.voucher_added'));
    }

    public function show(string $type, StockVoucher $voucher)
    {
        $this->assertType($type);
        abort_unless($voucher->type === $type, 404);

        $voucher->load(['warehouse', 'destinationWarehouse', 'customer', 'creator', 'poster', 'items.material.unit']);

        $backUrl = BackNavigation::url(['type' => $type], 'warehouse.vouchers.index');

        return $this->moduleView('warehouse.vouchers.show', compact('voucher', 'type', 'backUrl'));
    }

    public function post(Request $request, string $type, StockVoucher $voucher)
    {
        $this->assertType($type);
        abort_unless($voucher->type === $type, 404);

        try {
            $voucher->post($request->user());
        } catch (\RuntimeException $e) {
            return back()->with('error', __($e->getMessage()));
        }

        return back()->with('success', __('warehouse.voucher_posted'));
    }

    private function assertType(string $type): void
    {
        abort_unless(in_array($type, self::TYPES, true), 404);
    }
}
