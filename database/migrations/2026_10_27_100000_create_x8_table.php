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
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('x8');
    }
};
