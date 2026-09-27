<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_report_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained('materials')->restrictOnDelete();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->boolean('status')->default(true);
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_report_templates');
    }
};
