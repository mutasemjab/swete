<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Where a document reaches: exactly one of the target columns is set, matching `level`.
    // The matching target<->level pairing is enforced in app code (DocumentController::validated()),
    // not a DB CHECK constraint, to stay portable across MySQL/MariaDB versions.
    public function up(): void
    {
        Schema::create('document_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->cascadeOnDelete();
            $table->enum('level', ['unit', 'model', 'subtype', 'category', 'manufacturer', 'all']);
            $table->foreignId('equipment_unit_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('equipment_model_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('equipment_subtype_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('equipment_category_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('equipment_manufacturer_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_assignments');
    }
};
