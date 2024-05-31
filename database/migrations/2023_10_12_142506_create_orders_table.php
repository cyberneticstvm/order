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
            $table->bigInteger('order_sequence')->nullable();
            $table->date('order_date')->nullable();
            $table->unsignedBigInteger('consultation_id')->comment('0 if outside order')->nullable();
            $table->unsignedBigInteger('registration_id')->nullable();
            $table->unsignedBigInteger('spectacle_id')->nullable();
            $table->string('name', 55)->comment('Customer Name only applicable if outside order')->nullable();
            $table->integer('age')->nullable();
            $table->string('place', 100)->nullable();
            $table->string('mobile', 10)->nullable();
            $table->string('alt_mobile', 10)->nullable();
            $table->string('int_add', 7)->nullable();
            $table->string('a_size', 3)->nullable();
            $table->string('b_size', 3)->nullable();
            $table->string('dbl', 3)->nullable();
            $table->string('fh', 3)->nullable();
            $table->string('ed', 3)->nullable();
            $table->string('vd', 3)->nullable();
            $table->string('w_angle', 3)->nullable();
            $table->text('special_lab_note')->nullable();
            $table->string('invoice_number')->unique()->nullable();
            $table->dateTime('invoice_generated_at')->nullable();
            $table->unsignedBigInteger('invoice_generated_by')->nullable();
            $table->enum('category', ['store', 'pharmacy', 'service', 'other', 'solution']);
            $table->unsignedBigInteger('branch_id');
            $table->decimal('order_total', 9, 2)->default(0);
            $table->decimal('discount', 7, 2)->nullable();
            $table->decimal('invoice_total', 9, 2)->comment('total-discount')->default(0);
            $table->decimal('advance', 9, 2)->nullable();
            $table->decimal('credit_used', 9, 2)->nullable();
            $table->decimal('balance', 9, 2)->nullable();
            $table->enum('order_status', ['booked', 'under-process', 'pending', 'ready-for-delivery', 'delivered'])->default('booked');
            $table->enum('case_type', ['box', 'rexine', 'other'])->nullable();
            $table->unsignedBigInteger('product_adviser')->nullable();
            $table->date('expected_delivery_date')->nullable();
            $table->text('order_note')->nullable();
            $table->text('lab_note')->nullable();
            $table->text('invoice_note')->nullable();
            $table->string('gstin', 50)->nullable();
            $table->string('company_name', 150)->nullable();
            $table->unsignedBigInteger('state')->nullable();
            $table->enum('type', ['btob', 'btoc', 'other'])->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->timestamps();
            $table->softDeletes();
            $table->dateTime('stock_updated_at')->nullable();
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
