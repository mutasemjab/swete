<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_analyses', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            // Snapshotted from the branch at creation time — a later change to the branch's own
            // defaults must never alter an already-saved analysis (same spirit as maintenance report templates).
            $table->decimal('tax_rate', 5, 2);
            $table->decimal('jd_rate', 8, 4);
            $table->boolean('with_tax')->default(false);
            $table->text('notes')->nullable();
            // Optional link to the Tender/PriceQuote/Project this analysis was made for — same
            // polymorphic-by-convention shape as Agreement::party_type/party_id, not a real morph.
            // Set automatically when the analysis is created from that entity's own page (e.g. a
            // tender's "تحليل جديد" button) — never a manual picker on the analysis's own form.
            $table->enum('linked_type', ['tender', 'price_quote', 'project'])->nullable();
            $table->unsignedBigInteger('linked_id')->nullable();
            $table->boolean('is_current')->default(true);
            $table->unsignedInteger('version')->default(1);
            $table->foreignId('root_id')->nullable()->constrained('price_analyses')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_analyses');
    }
};
