<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Every alarm must cite a real manual + page — never invented, same discipline as the
    // refrigerant P-T chart in Phase 8. `key` is the normalized form of `code` (alphanumeric,
    // uppercase) computed in the model, used for fast exact/fuzzy lookup regardless of how a
    // manual prints the same code ("D4.1" vs "d41").
    public function up(): void
    {
        Schema::create('alarm_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('control_system_id')->constrained()->cascadeOnDelete();
            $table->string('code', 200);
            $table->string('key', 200)->index();
            $table->string('section', 120)->nullable();
            $table->string('name_en', 300);
            $table->string('name_ar', 300)->nullable();
            $table->text('causes_en')->nullable();
            $table->text('causes_ar')->nullable();
            $table->text('checks_en')->nullable();
            $table->text('checks_ar')->nullable();
            $table->text('effect_en')->nullable();
            $table->text('effect_ar')->nullable();
            $table->text('action_en')->nullable();
            $table->text('action_ar')->nullable();
            $table->text('reset_en')->nullable();
            $table->text('reset_ar')->nullable();
            $table->foreignId('source_document_id')->constrained('documents')->restrictOnDelete();
            $table->unsignedInteger('source_page');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // "manuals sometimes print two alarms under one code" — so (controller, key) alone
            // is not unique; the full tuple including name distinguishes legitimate duplicates.
            $table->unique(['control_system_id', 'key', 'source_document_id', 'name_en'], 'uniq_alarm_code_entry');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alarm_codes');
    }
};
