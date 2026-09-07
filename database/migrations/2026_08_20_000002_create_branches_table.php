<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('address_line1')->nullable();
            $table->string('address_line1_en')->nullable();
            $table->string('po_box')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('city')->nullable();
            $table->string('city_en')->nullable();
            $table->string('country')->nullable();
            $table->string('country_en')->nullable();
            $table->boolean('is_main')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
