<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('offer_categories', 'offer_type')) {
            Schema::table('offer_categories', function (Blueprint $table) {
                $table->string('offer_type', 30)->default('legacy')->after('name')->index();
            });
        }

        if (!Schema::hasTable('order_offers')) {
            Schema::create('order_offers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id')->unique();
                $table->unsignedBigInteger('offer_category_id');
                $table->unsignedBigInteger('lens_product_id');
                $table->unsignedBigInteger('frame_product_id')->nullable();
                $table->decimal('discount_percentage', 5, 2);
                $table->decimal('discount_amount', 9, 2);
                $table->timestamps();
                $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
                $table->foreign('offer_category_id')->references('id')->on('offer_categories');
                $table->foreign('lens_product_id')->references('id')->on('products');
                $table->foreign('frame_product_id')->references('id')->on('products');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_offers');
        if (Schema::hasColumn('offer_categories', 'offer_type')) {
            Schema::table('offer_categories', function (Blueprint $table) {
                $table->dropColumn('offer_type');
            });
        }
    }
};
