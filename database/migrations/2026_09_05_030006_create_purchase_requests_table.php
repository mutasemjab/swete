<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            // Both null = a general/stock request, not tied to a project or service call.
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('service_call_id')->nullable()->constrained('service_calls')->nullOnDelete();
            $table->date('date');
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
            // Request address is derived from the branch (Branch::localized_address_lines), not duplicated here.
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            // Shipping address — where the goods actually go, independent of the branch (e.g. a job site).
            $table->string('shipping_address_line1')->nullable();
            $table->string('shipping_address_line1_en')->nullable();
            $table->string('shipping_po_box')->nullable();
            $table->string('shipping_postal_code')->nullable();
            $table->string('shipping_city')->nullable();
            $table->string('shipping_city_en')->nullable();
            $table->string('shipping_country')->nullable();
            $table->string('shipping_country_en')->nullable();
            $table->enum('location_scope', ['inside_jordan', 'outside_jordan'])->nullable();
            $table->string('governorate')->nullable();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->decimal('subtotal', 14, 3)->default(0);
            $table->decimal('total', 14, 3)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_requests');
    }
};
