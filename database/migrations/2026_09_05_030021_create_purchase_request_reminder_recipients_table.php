<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // The Settings-managed pool of users notified whenever a new purchase-request
    // reminder is submitted — same shape/purpose as purchase_request_approvers.
    public function up(): void
    {
        Schema::create('purchase_request_reminder_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_request_reminder_recipients');
    }
};
