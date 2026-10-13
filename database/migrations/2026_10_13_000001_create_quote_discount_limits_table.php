<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Singleton settings record (always exactly one row) — see App\Models\QuoteDiscountLimit.
        Schema::create('quote_discount_limits', function (Blueprint $table) {
            $table->id();
            $table->decimal('max_discount_percent', 5, 2)->nullable();
            $table->decimal('max_discount_amount', 14, 3)->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_discount_limits');
    }
};
