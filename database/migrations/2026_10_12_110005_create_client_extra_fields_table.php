<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_extra_fields', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type', 20); // 'customer' | 'supplier'
            $table->string('label_ar', 150);
            $table->string('label_en', 150)->nullable();
            $table->string('field_type', 20)->default('text'); // text, number, date, textarea, image
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_extra_fields');
    }
};
