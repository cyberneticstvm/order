<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->nullable();
            $table->string('qr_code_text')->nullable();
            $table->integer('consultaton_fee_waived_days')->default(0);
            $table->time('appointment_starts_at')->nullable();
            $table->time('appointment_ends_at')->nullable();
            $table->integer('per_appointment_minutes')->default(0);
            $table->string('drug_license_number')->nullable();
            $table->integer('branch_limit')->comment('0-unlimited')->default(0);
            $table->boolean('allow_sales_at_zero_qty')->comment('1-yes, 0-no')->default(0);
            $table->enum('tax_type', ['VAT', 'GST', 'OTHER'])->default('GST');
            $table->string('currency')->default('₹')->nullable();
            $table->decimal('daily_expense_limit')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
