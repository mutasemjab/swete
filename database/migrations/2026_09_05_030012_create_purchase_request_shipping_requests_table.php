<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Audit trail of which shipping companies were actually emailed for which PR.
    public function up(): void
    {
        Schema::create('purchase_request_shipping_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id')->constrained('purchase_requests')->cascadeOnDelete();
            $table->foreignId('shipping_company_id')->constrained('shipping_companies')->restrictOnDelete();
            $table->foreignId('sent_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('sent_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_request_shipping_requests');
    }
};
