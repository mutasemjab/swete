<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // "x8" is the HEADER — one per Price Analysis (the whole X8 page for that one analysis), not
    // one per item. Its line-by-line data lives in x8_items (see create_x8_items_table), the same
    // header/items split as price_analyses/price_analysis_items.
    public function up(): void
    {
        Schema::create('x8', function (Blueprint $table) {
            $table->id();
            // NOT unique at the DB level: PriceAnalysis::archiveCurrentVersion() clones this header
            // (and its number, unchanged) onto every archived revision, so the same number
            // legitimately exists on more than one row once an analysis has been revised.
            $table->string('number');
            $table->foreignId('price_analysis_id')->unique()->constrained('price_analyses')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('x8');
    }
};
