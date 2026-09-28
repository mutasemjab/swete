<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per approver, snapshotted from maintenance_report_material_approvers at the moment a
        // report's materials-used lines are saved. All rows must be non-pending before the report's
        // materials_approval_status advances — same shape as purchase_request_approvals.
        Schema::create('maintenance_report_material_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('maintenance_reports')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('decision', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('decided_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_report_material_approvals');
    }
};
