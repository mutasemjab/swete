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
            // NOT unique at the DB level: every archived revision clone KEEPS the number it had at
            // the moment it was frozen (e.g. "X8-2026-00001-R0"), while the live header moves onto
            // its own next revision number ("-R1", "-R2", ...) — same base-number+suffix scheme as
            // PriceAnalysis/PriceQuote, just without their own is_current/root_id columns, since
            // whether a given X8 row is "live" or archived is already fully determined by whether
            // its price_analysis_id points at an is_current analysis or not.
            $table->string('number');
            $table->unsignedInteger('version')->default(1);
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
