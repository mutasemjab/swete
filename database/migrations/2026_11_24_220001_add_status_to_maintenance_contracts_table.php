<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_contracts', function (Blueprint $table) {
            // Defaults to 'active' so every pre-existing contract keeps behaving exactly as
            // before (visible, payable, schedulable) with zero migration step required —
            // only new contracts start as 'draft' until equipment + a schedule are set up.
            $table->enum('status', ['draft', 'active', 'suspended', 'terminated'])->default('active')->after('path');
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_contracts', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
