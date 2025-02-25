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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->enum('customer', ['hospital', 'store', 'lab', 'other']);
            $table->date('date')->nullable();
            $table->string('po_number', 25)->unique();
            $table->text('to')->nullable();
            $table->text('for')->nullable();
            $table->text('shipping_address')->nullable();
            $table->text('branch_address')->nullable();
            $table->decimal('sub_total', 10, 2)->default(0);
            $table->decimal('shipping', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('additional_expense', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->decimal('advance', 10, 2)->default(0);
            $table->decimal('balance', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->longText('terms')->nullable();
            $table->string('discount_remarks')->nullable();
            $table->string('additional_expense_remarks')->nullable();
            $table->unsignedBigInteger('advance_pmode')->nullable();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by');
            $table->foreign('advance_pmode')->references('id')->on('payment_modes')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
