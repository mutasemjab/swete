<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_reports', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            // Nullable + name/material snapshotted directly below: the template may be edited or
            // deleted years later without ever changing what an already-filled report displays.
            $table->foreignId('template_id')->nullable()->constrained('maintenance_report_templates')->nullOnDelete();
            $table->string('template_name');
            $table->foreignId('material_id')->nullable()->constrained('materials')->nullOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->date('date');
            // Fixed on every report regardless of its template's own custom questions.
            $table->text('problem')->nullable();
            $table->text('solution')->nullable();
            $table->text('notes')->nullable();
            // 'none' when the report has no materials-used lines at all; 'pending' once the approver
            // pool is seeded; 'approved'/'rejected' once every approver has decided. Approval triggers
            // an automatic posted stock-issue voucher — see MaintenanceReport::createIssueVoucher().
            $table->enum('materials_approval_status', ['none', 'pending', 'approved', 'rejected'])->default('none');
            $table->foreignId('issue_voucher_id')->nullable()->constrained('stock_vouchers')->nullOnDelete();
            // Set once this report has been converted into a price quote — see ReportController::convertToQuote().
            $table->foreignId('price_quote_id')->nullable()->constrained('price_quotes')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            // Groups this report under one technician visit — see App\Models\MaintenanceVisit.
            // Left unconstrained here (maintenance_visits is created later in the migration order);
            // the real FK is added at the bottom of that table's own migration. Null means a legacy/
            // desktop ad-hoc report created outside the visit workflow.
            $table->unsignedBigInteger('visit_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_reports');
    }
};
