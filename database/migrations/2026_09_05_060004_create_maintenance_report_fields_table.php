<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A per-report, permanent COPY of the template's fields at fill-time (question/type/options and
        // order are all duplicated here, not referenced) plus this report's own answer — this is what
        // makes old reports immune to later template edits.
        Schema::create('maintenance_report_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('maintenance_reports')->cascadeOnDelete();
            $table->string('question');
            $table->string('question_en')->nullable();
            $table->enum('type', ['number', 'text', 'boolean', 'choice']);
            $table->json('options')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->text('answer')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_report_fields');
    }
};
