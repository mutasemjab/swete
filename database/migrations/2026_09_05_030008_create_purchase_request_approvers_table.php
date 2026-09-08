<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // The Settings-managed pool of users who must unanimously approve every purchase
    // request. Not per-request — see purchase_request_approvals for the per-PR snapshot.
    public function up(): void
    {
        Schema::create('purchase_request_approvers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_request_approvers');
    }
};
