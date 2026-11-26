<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_schedule_units', function (Blueprint $table) {
            $table->foreignId('schedule_id')->constrained('maintenance_contract_schedules')->cascadeOnDelete();
            $table->foreignId('equipment_unit_id')->constrained('equipment_units')->restrictOnDelete();
            $table->primary(['schedule_id', 'equipment_unit_id']);
        });
        // Preserve the units already covered; do not regenerate or delete historical visits.
        DB::table('maintenance_contract_schedules')->where('scope', 'general')->where('include_new_units', false)
            ->orderBy('id')->each(function ($schedule) {
                foreach (DB::table('maintenance_contract_units')->where('contract_id', $schedule->contract_id)->whereNull('removed_at')->pluck('equipment_unit_id') as $unitId) {
                    DB::table('maintenance_schedule_units')->insertOrIgnore(['schedule_id' => $schedule->id, 'equipment_unit_id' => $unitId]);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_schedule_units');
    }
};
