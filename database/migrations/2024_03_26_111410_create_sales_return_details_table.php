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
        Schema::create('sales_return_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('return_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('order_qty')->nullable();
            $table->integer('returned_qty')->nullable();
            $table->foreign('return_id')->references('id')->on('sales_returns')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_return_details');
    }
};
