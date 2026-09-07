<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Table only, for now — the "الصيانة" (Maintenance) module UI comes later.
    public function up(): void
    {
        Schema::create('service_calls', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->enum('status', ['open', 'in_progress', 'closed'])->default('open');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_calls');
    }
};
