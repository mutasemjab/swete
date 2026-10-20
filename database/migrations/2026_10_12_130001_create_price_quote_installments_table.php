<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_quote_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('price_quote_id')->constrained()->cascadeOnDelete();
            $table->string('description', 255)->nullable();
            $table->decimal('percentage', 5, 2);
            $table->smallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_quote_installments');
    }
};
