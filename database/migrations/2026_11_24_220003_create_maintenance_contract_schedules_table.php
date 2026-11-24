<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // The recurrence rule that drives auto visit-generation. "general" applies to every unit
    // on the contract (unless opted out); "unit" overrides the general rule for one unit —
    // see App\Support\ContractVisitSync::scheduleForUnit() for the "individual beats general" rule.
    public function up(): void
    {
        Schema::create('maintenance_contract_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('maintenance_contracts')->cascadeOnDelete();
            $table->enum('scope', ['general', 'unit']);
            $table->foreignId('equipment_unit_id')->nullable()->constrained()->cascadeOnDelete();
            $table->enum('mode', ['interval', 'manual'])->default('interval');
            $table->unsignedSmallInteger('interval_value')->default(3);
            $table->enum('interval_unit', ['day', 'week', 'month'])->default('month');
            $table->json('manual_dates')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('include_new_units')->default(true); // general schedules only
            $table->foreignId('engineer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_contract_schedules');
    }
};
