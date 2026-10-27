<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('x8', function (Blueprint $table) {
            $table->id();
            $table->foreignId('price_analysis_item_id')->unique()->constrained('price_analysis_items')->cascadeOnDelete();
            $table->decimal('required_discount_percent', 5, 2)->nullable();
            $table->text('notes')->nullable();
            // Approval workflow for the "X8" module — rejecting always pairs with a new proposed
            // required_discount_percent (a counter-offer), so the item goes back to 'pending'
            // rather than parking as a dead-end 'rejected' state.
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('x8');
    }
};
