<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained('materials')->restrictOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->enum('type', ['in', 'out']);
            $table->decimal('quantity', 14, 3);
            $table->foreignId('stock_voucher_id')->nullable()->constrained('stock_vouchers')->nullOnDelete();
            $table->timestamp('moved_at');
            $table->timestamps();

            $table->index(['material_id', 'warehouse_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_stock_movements');
    }
};
