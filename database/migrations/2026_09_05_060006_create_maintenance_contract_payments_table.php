<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_contract_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('maintenance_contracts')->cascadeOnDelete();
            $table->date('due_date');
            $table->decimal('amount', 14, 3);
            $table->foreignId('currency_id')->constrained('currencies')->restrictOnDelete();
            $table->text('notes')->nullable();
            // Same "send to one named employee, they one-click convert" shape as price_quotes'
            // assigned_to/invoice_id — see PriceQuoteController::assign()/convertToInvoice().
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_contract_payments');
    }
};
