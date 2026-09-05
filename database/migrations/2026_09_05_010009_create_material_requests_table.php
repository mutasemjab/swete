<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_requests', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
            $table->date('needed_by_date')->nullable();
            $table->text('reason')->nullable();
            $table->enum('status', ['draft', 'pending_approval', 'approved', 'rejected', 'fulfilled', 'cancelled'])->default('draft');
            $table->foreignId('fulfilled_stock_voucher_id')->nullable()->constrained('stock_vouchers')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_requests');
    }
};
