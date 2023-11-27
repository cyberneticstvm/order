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
        Schema::create('medical_record_pharmacies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medical_record_id');
            $table->unsignedBigInteger('product_type')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->string('dosage')->nullable();
            $table->string('duration')->nullable();
            $table->enum('eye', ['left', 'right', 'both'])->nullable();
            $table->integer('qty')->nullable();
            $table->text('notes')->nullable();
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('medical_record_id')->references('id')->on('medical_records')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_record_pharmacies');
    }
};
