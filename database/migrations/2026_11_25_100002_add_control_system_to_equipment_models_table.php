<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipment_models', function (Blueprint $table) {
            $table->foreignId('control_system_id')->nullable()->after('equipment_subtype_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('equipment_models', function (Blueprint $table) {
            $table->dropConstrainedForeignId('control_system_id');
        });
    }
};
