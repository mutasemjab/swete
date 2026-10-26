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
            // Per-line choice — a single analysis can freely mix CIAT-discount lines and plain
            // warehouse-material lines (not a whole-analysis setting).
            $table->enum('source_type', ['ciat', 'warehouse'])->default('ciat');
            // Nullable + material/discount snapshotted directly below: a later edit or deletion of
            // the CIAT discount record (or the material it points at) must never change an
            // already-saved analysis line.
            $table->foreignId('ciat_discount_id')->nullable()->constrained('ciat_discounts')->nullOnDelete();
            $table->foreignId('material_id')->nullable()->constrained('materials')->nullOnDelete();
            // The specific CIAT model — typed per line, not looked up (only the material + its
            // discount % come from ciat_discounts). Nullable since a warehouse-sourced line has no
            // CIAT model at all.
            $table->string('ciat_model')->nullable();
            $table->decimal('quantity', 14, 3);
            $table->decimal('list_price', 14, 3);
            $table->decimal('discount_percent', 5, 2);
            // Snapshotted from the CiatDiscount at save time (0 for a warehouse-sourced line) —
            // feeds directly into `cost` below, same reasoning as discount_percent.
            $table->decimal('min_profit_percent', 5, 2)->default(0);
            $table->decimal('cost', 14, 3);
            // Profit is entered as a % markup on cost, not a flat amount — `profit` below is the
            // computed flat amount (still what total_profit/price/to_jd are built from).
            $table->decimal('profit_percent', 6, 2)->nullable();
            $table->decimal('profit', 14, 3);
            $table->decimal('total_profit', 14, 3);
            $table->decimal('price', 14, 3);
            $table->decimal('to_jd', 14, 3);
            $table->decimal('shipping', 14, 3)->default(0);
            // Quick-edit fields surfaced on the Tender's own show page (outside the versioned
            // Price Analysis edit flow) — edited directly in place, no archiving/versioning.
            $table->decimal('required_discount_percent', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_analysis_items');
    }
};
