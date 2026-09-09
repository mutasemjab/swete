<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // A singleton settings record — always exactly one row (see VendorEmailTemplateController).
    // The default text sent to a supplier when a purchase request is marked "Sent";
    // editable per-request on the show page before actually sending.
    public function up(): void
    {
        Schema::create('vendor_email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->text('body');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_email_templates');
    }
};
