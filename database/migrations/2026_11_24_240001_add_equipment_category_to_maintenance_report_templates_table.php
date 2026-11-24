<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Loosens material_id to nullable (every existing row already has one, so nothing changes
    // for them) and adds an alternative key: a template can now target an EquipmentCategory
    // instead of a Material. Exactly one of the two is enforced at the app level
    // (ReportTemplateController::validated()), same approach as DocumentAssignment's level/target.
    public function up(): void
    {
        Schema::table('maintenance_report_templates', function (Blueprint $table) {
            $table->foreignId('material_id')->nullable()->change();
            $table->foreignId('equipment_category_id')->nullable()->after('material_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_report_templates', function (Blueprint $table) {
            $table->dropConstrainedForeignId('equipment_category_id');
            $table->foreignId('material_id')->nullable(false)->change();
        });
    }
};
