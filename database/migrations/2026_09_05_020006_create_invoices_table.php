<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_type_id')->constrained('invoice_types')->restrictOnDelete();
            // party_type/party_id point at either `customers` or `suppliers` — no FK constraint
            // possible since it's one of two tables; Invoice::party() resolves the right one.
            $table->enum('party_type', ['customer', 'supplier']);
            $table->unsignedBigInteger('party_id');
            $table->string('number')->unique();
            $table->date('date');
            $table->date('due_date')->nullable();
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->enum('status', ['draft', 'posted', 'cancelled'])->default('draft');
            $table->decimal('subtotal', 14, 3)->default(0);
            $table->decimal('tax_total', 14, 3)->default(0);
            $table->decimal('total', 14, 3)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index(['party_type', 'party_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
