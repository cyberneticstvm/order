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
        Schema::create('patient_procedure_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_procedure_id');
            $table->unsignedBigInteger('procedure_id');
            $table->decimal('fee', 9, 2)->default(0);
            $table->foreign('patient_procedure_id')->references('id')->on('patient_procedures')->onDelete('cascade');
            $table->foreign('procedure_id')->references('id')->on('procedures');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_procedure_details');
    }
};
