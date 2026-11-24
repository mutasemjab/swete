<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_type_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->enum('language', ['ar', 'en', 'both', 'other'])->default('ar');
            $table->string('revision')->nullable();
            $table->string('tags')->nullable();
            $table->string('path');
            $table->string('original_name')->nullable();
            $table->string('content_type')->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_archived')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
