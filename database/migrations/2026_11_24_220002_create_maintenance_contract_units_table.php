<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Which equipment units a contract covers. Soft-removed (removed_at set, row kept) so
    // history survives a unit being dropped from a contract later.
    public function up(): void
    {
        Schema::create('maintenance_contract_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('maintenance_contracts')->cascadeOnDelete();
            $table->foreignId('equipment_unit_id')->constrained()->restrictOnDelete();
            $table->timestamp('added_at')->useCurrent();
            $table->timestamp('removed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_contract_units');
    }
};
