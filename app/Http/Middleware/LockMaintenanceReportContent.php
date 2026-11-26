<?php

namespace App\Http\Middleware;

use App\Models\MaintenanceReport;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LockMaintenanceReportContent
{
    public function handle(Request $request, Closure $next)
    {
        if (! in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return $next($request);
        }
        $name = $request->route()->getName();
        if (! in_array($name, [
            'maintenance-reports.update', 'maintenance-reports.destroy', 'maintenance-reports.send-to-engineer', 'maintenance-reports.approve-content',
            'maintenance-visits.mobile.reports.update', 'maintenance-visits.mobile.reports.answer', 'maintenance-visits.mobile.reports.materials',
        ], true)) {
            return $next($request);
        }

        DB::beginTransaction();
        try {
            $key = $request->route('maintenanceReport') !== null ? 'maintenanceReport' : 'maintenance_report';
            $bound = $request->route($key);
            $report = MaintenanceReport::whereKey($bound instanceof MaintenanceReport ? $bound->id : $bound)->lockForUpdate()->firstOrFail();
            abort_if($report->isContentApproved(), 403);
            $request->route()->setParameter($key, $report);

            $response = $next($request);
            // Laravel may render a controller exception into an error response inside the
            // pipeline. A returned 4xx/5xx must roll back just like a thrown exception.
            if ($response->getStatusCode() >= 400) {
                DB::rollBack();
            } else {
                DB::commit();
            }
            return $response;
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
