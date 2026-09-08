<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('shipping_company_id')->constrained('shipping_companies')->restrictOnDelete();
            $table->enum('transport_mode', ['sea', 'land', 'air']);
            // Only relevant when transport_mode = sea / air respectively.
            $table->enum('sea_service_type', ['lcl', '20ft', '40ft', '40hc'])->nullable();
            $table->enum('air_service_type', ['express', 'air_freight'])->nullable();
            $table->enum('incoterm', ['exw', 'fca', 'fas', 'fob', 'cfr', 'cif', 'cpt', 'cip', 'dap', 'dpu', 'ddp'])->nullable();
            $table->decimal('price', 14, 3)->nullable();
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->boolean('is_hazardous')->default(false);
            $table->string('shipping_line')->nullable();
            $table->string('bill_of_lading_number')->nullable();
            $table->string('container_number')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
