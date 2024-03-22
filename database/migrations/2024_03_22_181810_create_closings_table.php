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
        Schema::create('closings', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->decimal('closing_balance', 8, 2)->default(0)->nullable();
            $table->unsignedBigInteger('branch')->references('id')->on('branches')->default(0);
            $table->unsignedBigInteger('closed_by')->references('id')->on('users')->nullable()->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('closings');
    }
};
