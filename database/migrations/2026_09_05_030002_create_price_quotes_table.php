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
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->date('date');
            $table->enum('status', ['draft', 'sent', 'accepted', 'rejected'])->default('draft');
            $table->decimal('subtotal', 14, 3)->default(0);
            $table->decimal('total', 14, 3)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_quotes');
    }
};
