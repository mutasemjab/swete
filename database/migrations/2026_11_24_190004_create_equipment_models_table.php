<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_manufacturer_id')->constrained()->restrictOnDelete();
            $table->foreignId('equipment_category_id')->constrained()->restrictOnDelete();
            $table->foreignId('equipment_subtype_id')->nullable()->constrained()->nullOnDelete();
            $table->string('model_number');
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->unique(['equipment_manufacturer_id', 'model_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_models');
    }
};
