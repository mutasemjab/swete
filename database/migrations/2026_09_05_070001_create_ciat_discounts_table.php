<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ciat_discounts', function (Blueprint $table) {
            $table->id();
            $table->string('ciat_type');
            $table->string('ciat_model');
            $table->decimal('discount_percent', 5, 2);
            $table->boolean('status')->default(true);
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ciat_discounts');
    }
};
