<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_import_runs', function (Blueprint $table) {
            $table->id();
            $table->string('source', 40)->unique();
            $table->string('sha256', 64);
            $table->string('mapping_sha256', 64);
            $table->timestamp('completed_at')->nullable();
        });
        Schema::create('maintenance_import_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('run_id')->constrained('maintenance_import_runs')->restrictOnDelete();
            $table->string('source_table', 70);
            $table->unsignedBigInteger('source_id');
            $table->string('target_table', 70)->nullable();
            $table->unsignedBigInteger('target_id')->nullable();
            $table->json('payload');
            $table->unique(['run_id', 'source_table', 'source_id'], 'maintenance_import_source_unique');
            $table->index(['target_table', 'target_id']);
        });
        Schema::create('maintenance_import_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_record_id')->unique()->constrained('maintenance_import_records')->restrictOnDelete();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('equipment_unit_id')->constrained()->restrictOnDelete();
            $table->foreignId('report_id')->nullable()->constrained('maintenance_reports')->nullOnDelete();
            $table->string('number')->nullable();
            $table->date('scheduled_date');
            $table->date('actual_date')->nullable();
            $table->string('status', 30);
            $table->string('visit_type', 30);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_import_visits');
        Schema::dropIfExists('maintenance_import_records');
        Schema::dropIfExists('maintenance_import_runs');
    }
};
