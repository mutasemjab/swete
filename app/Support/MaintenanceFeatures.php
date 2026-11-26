<?php

namespace App\Support;

use Illuminate\Support\Facades\Schema;

class MaintenanceFeatures
{
    /** Keeps the old portal usable during deployments before additive migrations finish. */
    public static function ready(): bool
    {
        return Schema::hasTable('maintenance_notifications')
            && Schema::hasColumn('maintenance_reports', 'approved_pdf_path');
    }
}
