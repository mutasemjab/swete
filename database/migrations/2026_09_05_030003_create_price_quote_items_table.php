<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_quote_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('price_quote_id')->constrained('price_quotes')->cascadeOnDelete();
            $table->foreignId('material_id')->constrained('materials')->restrictOnDelete();
            $table->decimal('quantity', 14, 3);
            $table->decimal('unit_price', 14, 3)->default(0);
            $table->decimal('total', 14, 3)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_quote_items');
    }
};
