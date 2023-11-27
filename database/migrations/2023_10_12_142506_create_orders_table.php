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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->date('order_date')->nullable();
            $table->unsignedBigInteger('consultation_id')->comment('0 if outside order')->nullable();
            $table->string('name', 55)->comment('Customer Name only applicable if outside order')->nullable();
            $table->integer('age')->nullable();
            $table->string('place', 100)->nullable();
            $table->string('mobile', 10)->nullable();
            $table->string('invoice_number')->unique();
            $table->enum('category', ['store', 'pharmacy', 'service', 'other']);
            $table->unsignedBigInteger('branch_id');
            $table->decimal('order_total', 9, 2)->default(0);
            $table->decimal('discount', 7, 2)->nullable();
            $table->decimal('invoice_total', 9, 2)->comment('total-discount')->default(0);
            $table->decimal('advance', 9, 2)->nullable();
            $table->decimal('balance', 9, 2)->nullable();
            $table->enum('order_status', ['booked', 'under-process', 'pending', 'ready-for-delivery', 'delivered'])->default('booked');
            $table->enum('case_type', ['box', 'rexine', 'other'])->nullable();
            $table->unsignedBigInteger('product_adviser')->nullable();
            $table->date('expected_delivery_date')->nullable();
            $table->text('order_note')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
