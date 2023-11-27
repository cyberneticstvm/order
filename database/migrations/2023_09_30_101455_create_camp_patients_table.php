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
        Schema::create('camp_patients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('camp_id');
            $table->string('name', 50);
            $table->integer('age');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->string('place', 125);
            $table->string('mobile', 10);
            $table->boolean('further_investigation_advised')->comment('1-yes, 0-no')->default(0);
            $table->boolean('galsses_advised')->comment('1-yes, 0-no')->default(0);
            $table->boolean('yearly_eye_test_advised')->comment('1-yes, 0-no')->default(0);
            $table->boolean('surgery_advised')->comment('1-yes, 0-no')->default(0);
            $table->date('review_date')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('camp_id')->references('id')->on('camps')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camp_patients');
    }
};
