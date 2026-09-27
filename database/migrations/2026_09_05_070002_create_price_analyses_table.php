<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_analyses', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            // Snapshotted from the branch at creation time — a later change to the branch's own
            // defaults must never alter an already-saved analysis (same spirit as maintenance report templates).
            $table->decimal('tax_rate', 5, 2);
            $table->decimal('jd_rate', 8, 4);
            $table->boolean('with_tax')->default(false);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_analyses');
    }
};
