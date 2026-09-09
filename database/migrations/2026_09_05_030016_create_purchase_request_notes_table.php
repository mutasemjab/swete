<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // The "Additional Notes" print block — a repeatable list of {label, value} pairs.
    // Starts pre-filled with a preset label set on the create form; the user can
    // remove whichever rows they don't need. Labels are free text, not an enum.
    public function up(): void
    {
        Schema::create('purchase_request_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id')->constrained('purchase_requests')->cascadeOnDelete();
            $table->string('label');
            $table->string('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_request_notes');
    }
};
