<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // A local warehouse purchase order from a supplier — converts into a PurchaseInvoice (see
    // create_purchase_invoices_table), which is the only thing that actually posts stock.
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->date('date');
            $table->text('notes')->nullable();
            $table->decimal('subtotal', 14, 3)->default(0);
            $table->decimal('total', 14, 3)->default(0);
            $table->enum('status', ['draft', 'converted'])->default('draft');
            // No ->constrained(): purchase_invoices (created by a LATER migration) nullably
            // references purchase_orders right back, so this side just can't be a real FK without
            // a circular dependency — set from the app side once converted.
            $table->unsignedBigInteger('purchase_invoice_id')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
