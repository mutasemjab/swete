<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('equipment_category_id')->constrained()->restrictOnDelete();
            $table->foreignId('equipment_subtype_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('equipment_model_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('location')->nullable();
            // Distinguishes multiple sites/phases for the same customer that may hold identical units.
            $table->string('project')->nullable();
            $table->enum('status', ['operational', 'maintenance', 'out_of_service', 'decommissioned'])->default('operational');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true); // archive flag, not a hard delete
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['customer_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_units');
    }
};
