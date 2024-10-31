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
        Schema::create('offer_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offer_category_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('branch_id');
            $table->unique(['offer_category_id', 'product_id']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_products');
    }
};
