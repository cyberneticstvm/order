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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->integer('age');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->string('place', 125);
            $table->string('mobile', 10);
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('doctor_id');
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->foreign('doctor_id')->references('id')->on('doctors');
            $table->unique(['branch_id', 'doctor_id', 'date', 'time']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
