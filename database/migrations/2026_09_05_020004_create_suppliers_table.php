<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_group_id')->nullable()->constrained('supplier_groups')->nullOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('tax_number')->nullable();
            $table->decimal('opening_balance', 14, 3)->default(0);

            $table->enum('location_scope', ['inside_jordan', 'outside_jordan'])->nullable();
            $table->string('governorate')->nullable();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();

            // Default shipping address for this supplier — same shape as
            // purchase_requests.shipping_* so a PR can auto-fill from it on select.
            $table->string('shipping_address_line1')->nullable();
            $table->string('shipping_address_line1_en')->nullable();
            $table->string('shipping_po_box')->nullable();
            $table->string('shipping_postal_code')->nullable();
            $table->string('shipping_city')->nullable();
            $table->string('shipping_city_en')->nullable();
            $table->string('shipping_country')->nullable();
            $table->string('shipping_country_en')->nullable();
            $table->text('shipping_instruction')->nullable();

            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
