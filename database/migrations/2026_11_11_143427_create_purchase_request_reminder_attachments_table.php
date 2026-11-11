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
        Schema::create('purchase_request_reminder_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_request_reminder_id');
            // Explicit short constraint name — the auto-generated one exceeds MySQL's 64-char identifier limit.
            $table->foreign('purchase_request_reminder_id', 'prr_attachments_reminder_id_fk')
                ->references('id')->on('purchase_request_reminders')->cascadeOnDelete();
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
        Schema::dropIfExists('purchase_request_reminder_attachments');
    }
};
