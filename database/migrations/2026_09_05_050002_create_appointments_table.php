<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('appointment_date');
            $table->foreignId('appointment_type_id')->constrained('appointment_types')->restrictOnDelete();
            // Optional: an appointment isn't always tied to a customer.
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            // The employee who sees it (and its reminder) when they open the system.
            $table->foreignId('assigned_to')->constrained('users')->restrictOnDelete();
            $table->enum('status', ['scheduled', 'completed'])->default('scheduled');
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index(['assigned_to', 'status', 'appointment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
