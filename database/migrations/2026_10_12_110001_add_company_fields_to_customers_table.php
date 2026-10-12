<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('company_name', 200)->nullable()->after('name_en');
            $table->string('company_name_en', 200)->nullable()->after('company_name');
            $table->string('commercial_registration', 100)->nullable()->after('company_name_en');
            $table->string('personal_id', 100)->nullable()->after('commercial_registration');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['company_name', 'company_name_en', 'commercial_registration', 'personal_id']);
        });
    }
};
