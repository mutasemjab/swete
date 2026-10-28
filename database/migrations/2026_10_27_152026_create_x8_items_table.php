<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // One row per analysis item under an "x8" header — the actual required-discount/notes/
    // approval decision for that specific PriceAnalysisItem. NOT unique on price_analysis_item_id:
    // since X8 now has its own revision chain independent of the item's own table, the same item
    // gets a fresh X8Item row under every X8 header revision.
    public function up(): void
    {
        Schema::create('x8_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('x8_id')->constrained('x8')->cascadeOnDelete();
            $table->foreignId('price_analysis_item_id')->constrained('price_analysis_items')->cascadeOnDelete();
            $table->decimal('required_discount_percent', 5, 2)->nullable();
            // Multiple free-text notes, same shape as PriceQuoteItem.notes — not a single string.
            $table->json('notes')->nullable();
            // Rejecting always pairs with a new proposed required_discount_percent (a counter-offer),
            // so the item goes back to 'pending' rather than parking as a dead-end 'rejected' state.
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('x8_items');
    }
};
