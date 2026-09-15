<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Unstructured, repeatable free-text notes per reminder line item — mirrors
    // purchase_request_item_features exactly, both fields optional here too.
    public function up(): void
    {
        Schema::create('purchase_request_reminder_item_features', function (Blueprint $table) {
            $table->id();
            // Column + constraint declared separately with a short explicit name — the
            // auto-generated one exceeds MySQL's 64-char identifier limit (same issue hit
            // on purchase_request_reminder_items, see that migration's comment).
            $table->foreignId('purchase_request_reminder_item_id');
            $table->foreign('purchase_request_reminder_item_id', 'pr_reminder_item_features_item_id_fk')
                ->references('id')->on('purchase_request_reminder_items')->cascadeOnDelete();
            $table->string('value');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_request_reminder_item_features');
    }
};
