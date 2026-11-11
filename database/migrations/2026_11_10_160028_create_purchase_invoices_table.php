<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Creating this IS the confirming action — store() also auto-creates and immediately posts a
    // quantities-only 'receipt' StockVoucher (see PurchaseInvoiceController::store()), so there's
    // no separate draft/posted status here the way stock_vouchers has.
    public function up(): void
    {
        Schema::create('purchase_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
            // The SUPPLIER's own invoice number — not this document's own `number` above.
            $table->string('supplier_invoice_number');
            $table->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
            $table->foreignId('stock_voucher_id')->nullable()->constrained('stock_vouchers')->nullOnDelete();
            $table->date('date');
            $table->text('notes')->nullable();
            $table->decimal('subtotal', 14, 3)->default(0);
            $table->decimal('total', 14, 3)->default(0);
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_invoices');
    }
};
