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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('consultation_id')->comment('0 if outside payment')->nullable();
            $table->unsignedBigInteger('patient_id')->comment('0 if outside customer')->nullable();
            $table->unsignedBigInteger('order_id')->comment('applicable only for advance payment type')->nullable();
            $table->decimal('amount', 8, 2)->default(0);
            $table->unsignedBigInteger('payment_mode');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('branch_id');
            $table->enum('payment_type', ['complete', 'advance', 'partial', 'balance', 'pharmacy', 'outside', 'other'])->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
