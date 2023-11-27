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
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('consultation_id')->unique();
            $table->text('patient_history')->nullable();
            $table->text('allergic_drugs')->nullable();
            $table->text('doctor_recommondation')->nullable();
            $table->boolean('surgery_advised')->comment('1-yes, 0-no')->default(0);
            $table->date('review_date')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->foreign('consultation_id')->references('id')->on('consultations')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
