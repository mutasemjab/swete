<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            // A project is normally created by converting a won tender
            // (Tenders\TenderController@convertToProject), so both are nullable
            // in case a project ever needs to be created without one.
            $table->foreignId('tender_id')->nullable()->constrained('tenders')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            // The specific quote this project was converted from (Tenders\TenderController@convertToProject) —
            // this is "the project's own quote"; a later price quote on the same tender is a Variation
            // Order (see project_variation_orders), never a second origin for this same project.
            $table->foreignId('price_quote_id')->nullable()->constrained('price_quotes')->nullOnDelete();
            $table->string('title');
            $table->string('title_en')->nullable();
            // 'under_review' = just converted from a tender, awaiting approval before it's a real,
            // active project — see Tenders\ProjectReviewController (its own module/nav section).
            $table->enum('status', ['under_review', 'active', 'completed', 'cancelled'])->default('active');
            $table->text('notes')->nullable();
            // Team assigned once the project is live — the manager is picked by whoever has edit
            // rights; the manager is then the only one allowed to pick the engineer/foreman (see
            // Tenders\ProjectController@assignTeam).
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('engineer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('foreman_id')->nullable()->constrained('users')->nullOnDelete();
            // Unused going forward (the whole-project auto-revert this backed was replaced by the
            // Variation Order flow — see project_variation_orders) — kept rather than dropped mid-session.
            $table->text('review_reason')->nullable();
            // Unused going forward — superseded by the real is_current/version/root_id versioning
            // below (the project's own number now carries "-P{n}" directly, like price quotes'
            // "-R{n}") — kept rather than dropped mid-session.
            $table->unsignedInteger('variation_round')->default(0);
            // Versioning: approving a Variation Order never merges it into the live row — it freezes
            // the pre-change state into a new, read-only row (is_current=false, number keeps its old
            // "-P{n}" suffix, root_id points back at the live row) and the live row (same id) moves
            // forward to the next "-P{n+1}" number, now driven solely by the newly-approved quote.
            // Exact mirror of price_quotes' own versioning columns — see PriceQuote::archiveCurrentVersion().
            $table->boolean('is_current')->default(true);
            $table->unsignedInteger('version')->default(1);
            $table->foreignId('root_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
