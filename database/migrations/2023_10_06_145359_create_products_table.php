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
            $table->enum('category', ['lens', 'frame', 'pharmacy', 'service', 'other']);
            $table->unsignedBigInteger('type_id')->nullable();
            $table->unsignedBigInteger('shape_id')->nullable();
            $table->string('material')->nullable();
            $table->string('property')->nullable();
            $table->string('index')->nullable();
            $table->string('color')->nullable();
            $table->unsignedBigInteger('coating_id')->nullable();
            $table->unsignedBigInteger('manufacturer_id')->nullable();
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
