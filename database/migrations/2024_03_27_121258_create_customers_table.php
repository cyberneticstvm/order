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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->nullable();
            $table->string('mobile', 10)->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('return_id')->nullable();
            $table->decimal('credit', 7, 2)->nullable();
            $table->decimal('debit', 7, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
