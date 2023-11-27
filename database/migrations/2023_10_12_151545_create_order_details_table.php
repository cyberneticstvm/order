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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_type')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->string('batch_number')->nullable();
            $table->integer('qty')->default(0);
            $table->string('dosage')->nullable();
            $table->string('duration')->nullable();
            $table->enum('eye', ['re', 'le', 'both', 'frame', 'service'])->nullable();
            $table->string('sph', 7)->nullable();
            $table->string('cyl', 7)->nullable();
            $table->string('axis', 7)->nullable();
            $table->string('add', 7)->nullable();
            $table->string('dia', 7)->nullable();
            $table->string('ipd', 7)->nullable();
            $table->enum('thickness', ['thin', 'maximum-thin', 'normal-thick', 'not-applicable'])->nullable();
            $table->decimal('unit_price', 8, 2)->default(0);
            $table->decimal('total', 9, 2)->default(0);
            $table->decimal('tax_percentage', 5, 2)->nullable();
            $table->decimal('tax_amount', 8, 2)->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
