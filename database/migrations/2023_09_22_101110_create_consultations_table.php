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
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->string('mrn', 20)->unique();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('doctor_id');
            $table->decimal('doctor_fee', 7, 2)->nullable();
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('consultation_type');
            $table->boolean('review')->comment('1-yes, 0-no')->default(0);
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('created_by')->default(0);
            $table->unsignedBigInteger('updated_by')->default(0);
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('doctors');
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->foreign('department_id')->references('id')->on('departments');
            $table->foreign('consultation_type')->references('id')->on('consultation_types');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
