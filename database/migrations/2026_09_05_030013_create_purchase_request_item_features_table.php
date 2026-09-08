<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Unstructured, repeatable free-text notes per line item (e.g. "24V", "stainless") —
    // deliberately no key/type column, just raw values.
    public function up(): void
    {
        Schema::create('purchase_request_item_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_item_id')->constrained('purchase_request_items')->cascadeOnDelete();
            $table->string('value');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_request_item_features');
    }
};
