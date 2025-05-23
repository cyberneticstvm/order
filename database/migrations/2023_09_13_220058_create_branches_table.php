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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name', 125)->unique();
            $table->string('code', 15)->unique();
            $table->string('type', 25)->comment('branch, own-lab, outside-lab')->nullable();
            $table->integer('ho_master')->default(0)->nullable();
            $table->string('phone', 25)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('address')->nullable();
            $table->string('gstin', 25)->nullable();
            $table->integer('display_capacity')->default(0);
            $table->decimal('monthly_target', 9, 2)->default(0);
            $table->integer('target_percentage')->default(0);
            $table->decimal('daily_expense_limit', 8, 2)->default(0)->nullable();
            $table->integer('cash_sales_id')->nullable();
            $table->integer('discount_limit_percentage')->default(0);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
