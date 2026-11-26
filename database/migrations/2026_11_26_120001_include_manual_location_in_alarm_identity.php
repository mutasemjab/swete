<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Keep both manual entries when a code/name occurs under different equipment sections.
        // Install the replacement before dropping the old index; no records are rewritten.
        Schema::table('alarm_codes', function (Blueprint $table) {
            $table->unique(['control_system_id', 'key', 'source_document_id', 'name_en', 'section', 'source_page'], 'uniq_alarm_code_location');
        });
        Schema::table('alarm_codes', function (Blueprint $table) {
            $table->dropUnique('uniq_alarm_code_entry');
        });
    }

    public function down(): void
    {
        // Refuse rollback if legitimate location variants would violate the old index.
        // Creating it first leaves the current index/data intact when that happens.
        Schema::table('alarm_codes', function (Blueprint $table) {
            $table->unique(['control_system_id', 'key', 'source_document_id', 'name_en'], 'uniq_alarm_code_entry');
        });
        Schema::table('alarm_codes', function (Blueprint $table) {
            $table->dropUnique('uniq_alarm_code_location');
        });
    }
};
