<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_rules', function (Blueprint $table) {
            $table->id();
            $table->string('route_name')->unique();
            $table->string('label')->nullable();
            $table->boolean('status')->default(true);
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('approval_rule_approvers', function (Blueprint $table) {
            $table->foreignId('approval_rule_id')->constrained('approval_rules')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->primary(['approval_rule_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_rule_approvers');
        Schema::dropIfExists('approval_rules');
    }
};
