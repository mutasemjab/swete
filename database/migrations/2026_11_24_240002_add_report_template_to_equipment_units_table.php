<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // A unit's own override — wins over its category's template. See App\Support\ReportTemplateResolver.
    public function up(): void
    {
        Schema::table('equipment_units', function (Blueprint $table) {
            $table->foreignId('report_template_id')->nullable()->after('qr_token')->constrained('maintenance_report_templates')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('equipment_units', function (Blueprint $table) {
            $table->dropConstrainedForeignId('report_template_id');
        });
    }
};
