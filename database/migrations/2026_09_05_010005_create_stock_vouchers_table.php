<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_vouchers', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['receipt', 'issue', 'transfer', 'stocktake']);
            $table->string('number')->unique();
            $table->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->foreignId('destination_warehouse_id')->nullable()->constrained('warehouses')->restrictOnDelete();
            // Only ever set on an `issue` voucher issued directly to a customer — nullable, since
            // MaterialRequest::fulfill() also auto-creates plain internal `issue` vouchers with no
            // customer at all. No DB-level FK: `customers` is created by a LATER migration
            // (2026_09_05_020003), so this table can't ->constrained() it without breaking
            // migration order — the Eloquent customer() relation below still works fine unconstrained.
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->date('date');
            $table->string('reference_no')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'posted', 'cancelled'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();

            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_vouchers');
    }
};
