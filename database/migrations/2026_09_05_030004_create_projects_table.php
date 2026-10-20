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
            // Set whenever an active project is bounced back to under_review because one of its
            // tender's price quotes changed — see Tenders\PriceQuoteController@revertProjectIfNeeded.
            $table->text('review_reason')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
