<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_request_reminder_items', function (Blueprint $table) {
            $table->id();
            // foreignId()->constrained() doesn't accept a custom constraint name in this Laravel version,
            // and the auto-generated one exceeds MySQL's 64-char identifier limit — so the column and the
            // constraint are declared separately here, with an explicit short constraint name.
            $table->foreignId('purchase_request_reminder_id');
            $table->foreign('purchase_request_reminder_id', 'pr_reminder_items_reminder_id_fk')
                ->references('id')->on('purchase_request_reminders')->cascadeOnDelete();
            $table->foreignId('material_id')->constrained('materials')->restrictOnDelete();
            $table->decimal('quantity', 14, 3);
            // Same fields as purchase_request_items, but all optional — the requester may not know
            // pricing/ERCD, and the officer who fulfills the reminder fills them in anyway.
            $table->string('ercd')->nullable();
            $table->decimal('unit_price', 14, 3)->nullable();
            $table->decimal('total', 14, 3)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_request_reminder_items');
    }
};
