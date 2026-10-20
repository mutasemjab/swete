<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Dropped the separate "company" concept entirely — `name` itself now doubles as
            // either a personal or a company name (see its updated form label). These two stay,
            // moved next to tax_number, and now hold an uploaded document photo's path, not typed text.
            $table->string('commercial_registration_path', 255)->nullable()->after('tax_number');
            $table->string('personal_id_path', 255)->nullable()->after('commercial_registration_path');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['commercial_registration_path', 'personal_id_path']);
        });
    }
};
