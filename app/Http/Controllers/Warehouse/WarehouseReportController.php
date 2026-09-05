<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\ModuleController;
use App\Models\MaterialCategory;
use App\Models\MaterialStock;
use App\Models\MaterialStockMovement;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseReportController extends ModuleController
{
    protected string $module = 'warehouse';

    /** Stock-on-hand per material/warehouse, flagging items under their minimum level. */
    public function materials(Request $request)
    {
        $query = MaterialStock::with(['material.category', 'material.unit', 'warehouse'])
            ->whereHas('material')
            ->where('quantity', '>', 0);

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->input('warehouse_id'));
        }

        if ($request->filled('category_id')) {
            $query->whereHas('material', fn ($q) => $q->where('category_id', $request->input('category_id')));
        }

        $stocks      = $query->get()->sortBy(fn ($s) => $s->material->localized_name);
        $warehouses  = Warehouse::orderBy('name')->get();
        $categories  = MaterialCategory::orderBy('name')->get();

        return $this->moduleView('warehouse.reports.materials', compact('stocks', 'warehouses', 'categories'));
    }

    /** Inbound (stock-in) report, sourced from posted receipt vouchers only. */
    public function stocktake(Request $request)
    {
        $query = MaterialStockMovement::with(['material.unit', 'warehouse', 'stockVoucher'])
            ->where('type', 'in')
            ->whereHas('stockVoucher', fn ($q) => $q->where('type', 'receipt'));

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->input('warehouse_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('moved_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('moved_at', '<=', $request->input('date_to'));
        }

        $movements  = $query->latest('moved_at')->paginate(30)->withQueryString();
        $warehouses = Warehouse::orderBy('name')->get();

        return $this->moduleView('warehouse.reports.stocktake', compact('movements', 'warehouses'));
    }
}
