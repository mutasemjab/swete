<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Purely additive. Defaulting every row (old and new) to 'received' changes nothing for
    // existing reports: the only NEW restriction this introduces is the terminal 'approved' lock
    // (see MaintenanceReport::canEditContent()), and nothing is ever auto-approved by this
    // migration — a report only reaches 'approved' by actually going through the new workflow.
    public function up(): void
    {
        Schema::table('maintenance_reports', function (Blueprint $table) {
            $table->enum('content_status', ['received', 'engineer', 'approved'])->default('received')->after('materials_approval_status');
            $table->text('foreman_note')->nullable()->after('content_status');
            $table->foreignId('foreman_reviewed_by')->nullable()->after('foreman_note')->constrained('users')->nullOnDelete();
            $table->timestamp('foreman_reviewed_at')->nullable()->after('foreman_reviewed_by');
            $table->foreignId('approved_by')->nullable()->after('foreman_reviewed_at')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by');
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_reports', function (Blueprint $table) {
            $table->dropColumn(['content_status', 'foreman_note', 'foreman_reviewed_by', 'foreman_reviewed_at', 'approved_by', 'approved_at']);
        });
    }
};
