<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_analysis_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('price_analysis_id')->constrained('price_analyses')->cascadeOnDelete();
            // Nullable + material/discount snapshotted directly below: a later edit or deletion of
            // the CIAT discount record (or the material it points at) must never change an
            // already-saved analysis line.
            $table->foreignId('ciat_discount_id')->nullable()->constrained('ciat_discounts')->nullOnDelete();
            $table->foreignId('material_id')->nullable()->constrained('materials')->nullOnDelete();
            // The specific CIAT model — typed per line, not looked up (only the material + its
            // discount % come from ciat_discounts).
            $table->string('ciat_model');
            $table->decimal('quantity', 14, 3);
            $table->decimal('list_price', 14, 3);
            $table->decimal('discount_percent', 5, 2);
            $table->decimal('cost', 14, 3);
            $table->decimal('profit', 14, 3);
            $table->decimal('total_profit', 14, 3);
            $table->decimal('price', 14, 3);
            $table->decimal('to_jd', 14, 3);
            $table->decimal('shipping', 14, 3)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_analysis_items');
    }
};
