<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Optional traceability link — purely additive, never required.
    public function up(): void
    {
        Schema::table('maintenance_reports', function (Blueprint $table) {
            $table->foreignId('equipment_unit_id')->nullable()->after('material_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_reports', function (Blueprint $table) {
            $table->dropConstrainedForeignId('equipment_unit_id');
        });
    }
};
