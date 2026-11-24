<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Purely additive: every existing row keeps schedule_id/equipment_unit_id null and
    // source='manual', so today's fully-manual rows are completely unaffected. Only rows a
    // Schedule actually generated ever get schedule_id set, and ContractVisitSync::sync() only
    // ever touches rows where schedule_id is not null — see App\Support\ContractVisitSync.
    public function up(): void
    {
        Schema::table('maintenance_contract_scheduled_visits', function (Blueprint $table) {
            $table->foreignId('equipment_unit_id')->nullable()->after('contract_id')->constrained()->cascadeOnDelete();
            $table->foreignId('schedule_id')->nullable()->after('equipment_unit_id')->constrained('maintenance_contract_schedules')->nullOnDelete();
            $table->enum('source', ['manual', 'general', 'unit'])->default('manual')->after('schedule_id');

            // MySQL unique indexes allow any number of NULLs per column, so this only ever
            // guards generated rows (both columns set) — manual rows (both null) are untouched.
            $table->unique(['equipment_unit_id', 'scheduled_date'], 'uniq_generated_scheduled_visit');
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_contract_scheduled_visits', function (Blueprint $table) {
            $table->dropUnique('uniq_generated_scheduled_visit');
            $table->dropColumn(['equipment_unit_id', 'schedule_id', 'source']);
        });
    }
};
