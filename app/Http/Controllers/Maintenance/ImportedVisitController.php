<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\ModuleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImportedVisitController extends ModuleController
{
    protected string $module = 'maintenance';

    public function index(Request $request)
    {
        $visits = null;
        if (Schema::hasTable('maintenance_import_visits')) {
            $query = DB::table('maintenance_import_visits as v')
                ->join('customers as c', 'c.id', '=', 'v.customer_id')
                ->join('equipment_units as u', 'u.id', '=', 'v.equipment_unit_id')
                ->join('maintenance_import_records as r', 'r.id', '=', 'v.import_record_id')
                ->select('v.*', 'c.name as customer_name', 'u.name as unit_name', 'r.payload');
            if ($request->filled('unit')) $query->where('v.equipment_unit_id', $request->integer('unit'));
            $visits = $query->orderByDesc('v.scheduled_date')->paginate(25)->withQueryString();
        }
        return $this->moduleView('maintenance.imported-visits', compact('visits'));
    }
}
