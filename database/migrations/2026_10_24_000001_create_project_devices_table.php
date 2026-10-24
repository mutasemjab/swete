<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('price_quote_item_id')->constrained('price_quote_items')->cascadeOnDelete();
            $table->date('supply_date')->nullable();
            $table->date('installation_date')->nullable();
            $table->date('warranty_start_date')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['project_id', 'price_quote_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_devices');
    }
};
