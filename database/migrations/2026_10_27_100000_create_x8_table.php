<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // "x8" is the HEADER — one per Price Analysis (the whole X8 page for that one analysis), not
    // one per item. Its line-by-line data lives in x8_items (see create_x8_items_table), the same
    // header/items split as price_analyses/price_analysis_items.
    //
    // X8 has its OWN independent revision chain (is_current/version/root_id, same shape as
    // PriceQuote/Project/PriceAnalysis) — approving/rejecting/saving X8 only revisions X8 itself,
    // it never touches or archives the underlying PriceAnalysis. Because of that, `price_analysis_id`
    // is NOT unique here: a given analysis keeps the same (never-archived-for-this-reason) row,
    // while X8 accumulates its own history of header rows pointing at it.
    public function up(): void
    {
        Schema::create('x8', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->unsignedInteger('version')->default(1);
            $table->boolean('is_current')->default(true);
            $table->foreignId('root_id')->nullable()->constrained('x8')->nullOnDelete();
            $table->foreignId('price_analysis_id')->constrained('price_analyses')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('x8');
    }
};
