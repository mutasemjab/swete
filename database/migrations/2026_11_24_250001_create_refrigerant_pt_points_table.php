<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Deliberately EMPTY by default — never pre-seeded with assumed refrigerant physics data.
    // An engineer enters these points from their own refrigerant manufacturer's P-T chart, with
    // the source noted, exactly mirroring the standing project rule: never invent a number the
    // manuals don't give; leave it empty until someone with the real chart fills it in.
    public function up(): void
    {
        Schema::create('refrigerant_pt_points', function (Blueprint $table) {
            $table->id();
            $table->string('refrigerant', 30); // e.g. "R410A", free text so any refrigerant can be added
            $table->decimal('temperature_c', 6, 2);
            $table->decimal('pressure_bar', 7, 3); // absolute pressure, bar
            $table->string('source', 255)->nullable(); // which manual/chart this point came from
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['refrigerant', 'temperature_c']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refrigerant_pt_points');
    }
};
