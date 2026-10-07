<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_contract_scheduled_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('maintenance_contracts')->cascadeOnDelete();
            $table->date('scheduled_date');
            $table->enum('type', ['emergency', 'periodic']);
            $table->text('notes')->nullable();
            // Guards the once-only reminder email — see MaintenanceContractScheduledVisit::scopeNeedingNotification().
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_contract_scheduled_visits');
    }
};
