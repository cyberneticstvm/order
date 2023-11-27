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
        Schema::create('medical_record_visions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medical_record_id');
            $table->string('re_sph', 6)->nullable();
            $table->string('re_cyl', 6)->nullable();
            $table->string('re_axis', 6)->nullable();
            $table->string('re_add', 6)->nullable();
            $table->string('re_va', 6)->nullable();
            $table->string('re_nv', 6)->nullable();
            $table->string('le_sph', 6)->nullable();
            $table->string('le_cyl', 6)->nullable();
            $table->string('le_axis', 6)->nullable();
            $table->string('le_add', 6)->nullable();
            $table->string('le_va', 6)->nullable();
            $table->string('le_nv', 6)->nullable();
            $table->foreign('medical_record_id')->references('id')->on('medical_records')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_record_visions');
    }
};
