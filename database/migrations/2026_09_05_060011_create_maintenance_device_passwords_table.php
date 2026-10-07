<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_device_passwords', function (Blueprint $table) {
            $table->id();
            $table->string('device_name');
            // Encrypted at rest — see App\Models\MaintenanceDevicePassword's 'encrypted' cast.
            $table->text('password');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_device_passwords');
    }
};
