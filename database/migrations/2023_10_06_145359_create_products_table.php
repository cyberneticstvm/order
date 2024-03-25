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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->enum('category', ['lens', 'frame', 'pharmacy', 'service', 'contact-lens', 'accessory', 'other']);
            $table->unsignedBigInteger('type_id')->nullable();
            $table->unsignedBigInteger('shape_id')->nullable();
            $table->unsignedBigInteger('material')->nullable();
            $table->string('property')->nullable();
            $table->string('index')->nullable();
            $table->unsignedBigInteger('color')->nullable();
            $table->string('eye_size', 25)->comment('Applicable for Frames only')->nullable();
            $table->string('bridge_size', 25)->comment('Applicable for Frames only')->nullable();
            $table->string('temple_size', 25)->comment('Applicable for Frames only')->nullable();
            $table->unsignedBigInteger('collection_id')->comment('Applicable for Frames only')->nullable();
            $table->unsignedBigInteger('coating_id')->nullable();
            $table->unsignedBigInteger('manufacturer_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->integer('reorder_level')->nullable();
            $table->decimal('tax_percentage', 5, 2)->nullable();
            $table->decimal('mrp', 7, 2)->nullable();
            $table->decimal('selling_price', 7, 2)->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists('products');
    }
};
