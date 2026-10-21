<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_quotes', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            // Nullable: a quote can be drafted before a tender exists, or reused,
            // then attached to a tender later (Tenders\TenderController@attachPriceQuote).
            $table->foreignId('tender_id')->nullable()->constrained('tenders')->nullOnDelete();
            // Per-tender counter for genuinely distinct quotes (never touched by an edit — editing
            // bumps `version`/R-label instead): 1 for the first quote ever created against a tender,
            // 2+ for each later one (a Variation Order once the tender already has an active project).
            $table->unsignedInteger('tender_sequence')->nullable();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->date('date');
            $table->enum('status', ['draft', 'sent', 'accepted', 'rejected'])->default('draft');
            $table->decimal('subtotal', 14, 3)->default(0);
            // What the user typed (a fixed amount or a percentage of the subtotal), plus the resolved
            // amount actually deducted — total = subtotal - discount_amount.
            $table->enum('discount_type', ['amount', 'percent'])->default('amount');
            $table->decimal('discount_value', 14, 3)->default(0);
            $table->decimal('discount_amount', 14, 3)->default(0);
            $table->decimal('total', 14, 3)->default(0);

            // Print-document fields — drives the letterhead/address (branch) and the "Very Important
            // Notes" clause block (see PriceQuote::getVeryImportantNotesAttribute()).
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->foreignId('currency_id')->constrained('currencies')->restrictOnDelete();
            $table->unsignedSmallInteger('validity_weeks')->nullable();
            $table->foreignId('supply_scope_id')->nullable()->constrained('quote_supply_scopes')->nullOnDelete();
            $table->foreignId('delivery_term_id')->nullable()->constrained('quote_delivery_terms')->nullOnDelete();
            $table->boolean('winching_included')->default(false);
            $table->boolean('sales_tax_included')->default(false);
            $table->boolean('customs_fees_included')->default(false);
            $table->boolean('include_boiler_note')->default(false);
            // Free-text custom points, one per line — appended to the auto-generated "Very Important
            // Notes" clause block on print, after the fixed toggles/selections above.
            $table->text('additional_terms')->nullable();

            $table->text('notes')->nullable();
            // The employee this quote was handed to for conversion — set via a bulk "send to
            // employee" action, cleared to null relationship-wise never (kept for history/audit).
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            // Set once the assignee converts this quote — non-null means "already invoiced",
            // used both to hide it from the assignee's pending list and to link to the result.
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();

            // Versioning: editing a quote never overwrites it — the live row (is_current=true) keeps
            // its id/number and just bumps `version`; the pre-edit state is cloned into a frozen row
            // (is_current=false, number suffixed "-vN", root_id pointing back at the live row) by
            // PriceQuote::archiveCurrentVersion(), called from the controller right before an update
            // is applied. See PriceQuoteController::update().
            $table->boolean('is_current')->default(true);
            $table->unsignedInteger('version')->default(1);
            $table->foreignId('root_id')->nullable()->constrained('price_quotes')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_quotes');
    }
};
