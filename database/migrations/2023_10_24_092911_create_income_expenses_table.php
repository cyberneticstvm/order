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
        Schema::create('income_expenses', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->unsignedBigInteger('head_id');
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->decimal('amount', 9, 2)->nullable();
            $table->unsignedBigInteger('branch_id');
            $table->foreign('head_id')->references('id')->on('heads')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
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
        Schema::dropIfExists('income_expenses');
    }
};
