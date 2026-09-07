<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenders', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('party_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->string('entity_name');
            $table->string('entity_name_en')->nullable();

            $table->enum('location_scope', ['inside_jordan', 'outside_jordan'])->nullable();
            $table->string('governorate')->nullable();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();

            $table->boolean('tax_exempt')->default(false);
            $table->boolean('customs_exempt')->default(false);
            $table->enum('delivery_terms', ['site', 'cfr', 'exwork'])->nullable();
            $table->enum('coverage', ['supply', 'supply_execution', 'design', 'design_execution', 'design_supply_execution'])->nullable();

            $table->text('description')->nullable();
            $table->unsignedTinyInteger('win_probability')->nullable();
            $table->date('submission_deadline');
            $table->foreignId('status_id')->constrained('tender_statuses')->restrictOnDelete();

            $table->string('documents_url')->nullable();
            $table->string('design_documents_url')->nullable();

            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenders');
    }
};
