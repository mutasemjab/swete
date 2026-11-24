<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipment_units', function (Blueprint $table) {
            $table->string('qr_token', 32)->nullable()->unique()->after('is_active');
        });

        // Backfill any units created before this column existed.
        DB::table('equipment_units')->whereNull('qr_token')->orderBy('id')->get(['id'])->each(function ($unit) {
            DB::table('equipment_units')->where('id', $unit->id)->update(['qr_token' => bin2hex(random_bytes(16))]);
        });
    }

    public function down(): void
    {
        Schema::table('equipment_units', function (Blueprint $table) {
            $table->dropColumn('qr_token');
        });
    }
};
