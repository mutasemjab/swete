<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_contracts', function (Blueprint $table) {
            $table->unsignedInteger('emergency_visit_limit')->nullable();
        });
        Schema::table('maintenance_visits', function (Blueprint $table) {
            $table->uuid('client_uuid')->nullable()->unique();
            $table->string('submission_hash', 64)->nullable();
            $table->foreignId('contract_id')->nullable()->constrained('maintenance_contracts')->restrictOnDelete();
            $table->foreignId('scheduled_visit_id')->nullable()->unique()->constrained('maintenance_contract_scheduled_visits')->restrictOnDelete();
            $table->string('kind', 20)->nullable();
        });
        Schema::table('maintenance_reports', function (Blueprint $table) {
            $table->uuid('client_uuid')->nullable()->unique();
            $table->json('technician_snapshot')->nullable();
            $table->string('technician_pdf_path')->nullable();
            $table->string('approved_pdf_path')->nullable();
        });
        Schema::table('maintenance_report_fields', function (Blueprint $table) {
            $table->string('field_key')->nullable();
            $table->string('section')->nullable();
        });
        Schema::create('maintenance_notification_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('kind', 30);
            $table->unique(['user_id', 'kind']);
        });
        Schema::create('maintenance_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('kind', 30);
            $table->string('dedupe_key', 120);
            $table->json('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamp('emailed_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'dedupe_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_notifications');
        Schema::dropIfExists('maintenance_notification_recipients');
        Schema::table('maintenance_report_fields', fn (Blueprint $table) => $table->dropColumn(['field_key', 'section']));
        Schema::table('maintenance_reports', function (Blueprint $table) {
            $table->dropUnique(['client_uuid']);
            $table->dropColumn(['client_uuid', 'technician_snapshot', 'technician_pdf_path', 'approved_pdf_path']);
        });
        Schema::table('maintenance_visits', function (Blueprint $table) {
            $table->dropConstrainedForeignId('contract_id');
            $table->dropForeign(['scheduled_visit_id']);
            $table->dropUnique(['scheduled_visit_id']);
            $table->dropUnique(['client_uuid']);
            $table->dropColumn(['scheduled_visit_id', 'client_uuid', 'submission_hash', 'kind']);
        });
        Schema::table('maintenance_contracts', fn (Blueprint $table) => $table->dropColumn('emergency_visit_limit'));
    }
};
