<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tender_document_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained('tenders')->cascadeOnDelete();
            $table->enum('kind', ['documents', 'design']);
            $table->string('path');
            $table->string('original_name');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tender_document_attachments');
    }
};
