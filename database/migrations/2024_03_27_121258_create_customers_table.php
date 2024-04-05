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
            $table->string('name', 100)->nullable();
            $table->unsignedBigInteger('mrn')->nullable();
            $table->integer('age')->nullable();
            $table->string('address', 150)->nullable();
            $table->string('mobile', 10)->nullable();
            $table->string('alt_mobile', 10)->nullable();
            $table->string('gstin', 50)->nullable();
            $table->string('company_name', 150)->nullable();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
