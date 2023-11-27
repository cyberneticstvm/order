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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('patient_id', 10)->unique();
            $table->string('name', 50);
            $table->integer('age');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->string('place', 125);
            $table->string('mobile', 10);
            $table->unsignedBigInteger('branch_id');
            $table->decimal('registration_fee', 7, 2)->nullable();            
            $table->enum('type', ['Appointment', 'Camp', 'Direct']);
            $table->unsignedBigInteger('type_id')->nullable();            
            $table->unsignedBigInteger('created_by')->default(0);
            $table->unsignedBigInteger('updated_by')->default(0);
            $table->foreign('branch_id')->references('id')->on('branches');            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
