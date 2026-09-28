<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The Settings-managed pool of users who must unanimously approve every report's materials-used
        // list before a stock-issue voucher gets created — same shape as purchase_request_approvers.
        Schema::create('maintenance_report_material_approvers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_report_material_approvers');
    }
};
