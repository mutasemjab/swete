<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment_subtypes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_category_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->unique(['equipment_category_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_subtypes');
    }
};
