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
        Schema::create('camp_patient_visions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('camp_patient_id');
            $table->string('re_vb', 6)->nullable();
            $table->string('re_sph', 6)->nullable();
            $table->string('re_cyl', 6)->nullable();
            $table->string('re_axis', 6)->nullable();
            $table->string('re_add', 6)->nullable();
            $table->string('re_va', 6)->nullable();
            $table->string('le_vb', 6)->nullable();
            $table->string('le_sph', 6)->nullable();
            $table->string('le_cyl', 6)->nullable();
            $table->string('le_axis', 6)->nullable();
            $table->string('le_add', 6)->nullable();
            $table->string('le_va', 6)->nullable();
            $table->foreign('camp_patient_id')->references('id')->on('camp_patients')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camp_patient_visions');
    }
};
