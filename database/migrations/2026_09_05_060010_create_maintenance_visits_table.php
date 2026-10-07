<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('maintenance_request_id')->nullable()->constrained('maintenance_requests')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            // Entered manually by the technician when starting the visit (not auto-stamped).
            $table->timestamp('check_in_at');
            // Stamped automatically the moment the visit is finished/submitted to the customer —
            // see MaintenanceVisit::finish(). Together with check_in_at this is the hours-worked figure.
            $table->timestamp('check_out_at')->nullable();
            $table->enum('status', ['open', 'submitted_to_customer', 'customer_signed', 'closed'])->default('open');
            $table->foreignId('visit_type_id')->nullable()->constrained('maintenance_visit_types')->nullOnDelete();
            $table->text('classification_note')->nullable();
            $table->foreignId('classified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('signed_at')->nullable();
            $table->string('signature_path')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });

        // Deferred FKs for columns that had to live in earlier migrations (per the dev-phase
        // policy, a column stays in its owning create-table migration even when the table it
        // references is created later) — see maintenance_reports.visit_id and
        // maintenance_requests.linked_visit_id.
        Schema::table('maintenance_reports', function (Blueprint $table) {
            $table->foreign('visit_id')->references('id')->on('maintenance_visits')->nullOnDelete();
        });

        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->foreign('linked_visit_id')->references('id')->on('maintenance_visits')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_reports', function (Blueprint $table) {
            $table->dropForeign(['visit_id']);
        });

        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->dropForeign(['linked_visit_id']);
        });

        Schema::dropIfExists('maintenance_visits');
    }
};
