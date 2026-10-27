<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // General attachments for the Tender's whole "X8" (Analysis Items) section — not per item,
    // same shape as project_attachments (real uploaded files via the shared uploadImage() helper).
    public function up(): void
    {
        Schema::create('tender_analysis_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained('tenders')->cascadeOnDelete();
            $table->string('name');
            $table->string('path');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tender_analysis_attachments');
    }
};
