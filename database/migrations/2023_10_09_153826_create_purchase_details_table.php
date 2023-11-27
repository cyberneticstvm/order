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
        Schema::create('purchase_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id');
            $table->unsignedBigInteger('product_id');
            $table->string('batch_number')->nullable();
            $table->date('expiry_date')->nullable();
            $table->integer('qty')->default(0);
            $table->decimal('unit_price_mrp', 7, 2)->nullable();
            $table->decimal('unit_price_purchase', 7, 2)->nullable();
            $table->decimal('unit_price_sales', 7, 2)->nullable();
            $table->decimal('total', 7, 2)->comment('unit_price_purchase*qty')->nullable();
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_details');
    }
};
