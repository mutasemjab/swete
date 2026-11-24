<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // The superheat "normal zone" band — a unit's own override wins over its model's, which
    // wins over the app-wide default. Stored as JSON so other reading types can be added later
    // (e.g. subcooling) without another migration.
    public function up(): void
    {
        Schema::table('equipment_units', function (Blueprint $table) {
            $table->json('reading_limits')->nullable()->after('report_template_id');
        });
        Schema::table('equipment_models', function (Blueprint $table) {
            $table->json('reading_limits')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('equipment_units', function (Blueprint $table) {
            $table->dropColumn('reading_limits');
        });
        Schema::table('equipment_models', function (Blueprint $table) {
            $table->dropColumn('reading_limits');
        });
    }
};
