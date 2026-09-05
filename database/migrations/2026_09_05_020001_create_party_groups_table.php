<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('party_groups', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['customer', 'supplier']);
            $table->foreignId('parent_id')->nullable()->constrained('party_groups')->nullOnDelete();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('party_groups');
    }
};
