<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_extra_field_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->constrained('client_extra_fields')->cascadeOnDelete();
            $table->string('entity_type', 20);
            $table->unsignedBigInteger('entity_id');
            $table->text('value')->nullable();
            $table->string('file_path', 500)->nullable();
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_extra_field_values');
    }
};
