<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // A control system (e.g. "Carel Connect 2") — alarm lists belong to the controller, not to
    // one equipment size/model, since the same board can sit in many different unit sizes.
    public function up(): void
    {
        Schema::create('control_systems', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->unique('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('control_systems');
    }
};
