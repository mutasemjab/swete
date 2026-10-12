<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('split_units', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->date('date');
            $table->enum('status', ['draft', 'sent', 'accepted', 'rejected'])->default('draft');
            $table->decimal('subtotal', 14, 3)->default(0);
            $table->enum('discount_type', ['amount', 'percent'])->default('amount');
            $table->decimal('discount_value', 14, 3)->default(0);
            $table->decimal('discount_amount', 14, 3)->default(0);
            $table->decimal('total', 14, 3)->default(0);
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->foreignId('currency_id')->constrained('currencies')->restrictOnDelete();
            $table->unsignedSmallInteger('validity_weeks')->nullable();
            $table->foreignId('supply_scope_id')->nullable()->constrained('quote_supply_scopes')->nullOnDelete();
            $table->foreignId('delivery_term_id')->nullable()->constrained('quote_delivery_terms')->nullOnDelete();
            $table->boolean('winching_included')->default(false);
            $table->boolean('sales_tax_included')->default(false);
            $table->boolean('customs_fees_included')->default(false);
            $table->boolean('include_boiler_note')->default(false);
            $table->json('included_work_scopes')->nullable();
            $table->text('additional_terms')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('split_units');
    }
};
