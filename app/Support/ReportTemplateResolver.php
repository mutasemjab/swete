<?php

namespace App\Support;

use App\Models\EquipmentUnit;
use App\Models\MaintenanceReportTemplate;

/** Which report template applies to a unit — its own override always wins over its category's. */
class ReportTemplateResolver
{
    public static function forUnit(EquipmentUnit $unit): ?MaintenanceReportTemplate
    {
        if ($unit->report_template_id && $unit->reportTemplate && $unit->reportTemplate->status) {
            return $unit->reportTemplate;
        }

        return MaintenanceReportTemplate::where('equipment_category_id', $unit->equipment_category_id)
            ->where('status', true)
            ->first();
    }
}
