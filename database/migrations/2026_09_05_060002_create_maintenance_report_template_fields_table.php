<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The live, editable question list a template currently has — filling out a NEW report always
        // copies from here. Editing/deleting rows here never touches an already-filled report's own
        // snapshot in maintenance_report_fields, by design.
        Schema::create('maintenance_report_template_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('maintenance_report_templates')->cascadeOnDelete();
            $table->string('question');
            $table->string('question_en')->nullable();
            $table->enum('type', ['number', 'text', 'boolean', 'choice', 'images']);
            // Only meaningful when type = 'choice' — the fixed list of selectable answers.
            $table->json('options')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_report_template_fields');
    }
};
