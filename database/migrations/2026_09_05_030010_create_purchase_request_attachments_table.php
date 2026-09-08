<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Manufacturing-stage attachments — plain links (mirrors Tender::documents_url's
    // style), not uploaded files. Repeatable, so its own table instead of one column.
    public function up(): void
    {
        Schema::create('purchase_request_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id')->constrained('purchase_requests')->cascadeOnDelete();
            $table->string('url');
            $table->string('label')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_request_attachments');
    }
};
