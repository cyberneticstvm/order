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
        Schema::create('spectacles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('re_sph', 7)->nullable();
            $table->string('re_cyl', 7)->nullable();
            $table->string('re_axis', 7)->nullable();
            $table->string('re_add', 7)->nullable();
            $table->string('re_va', 7)->nullable();
            $table->string('re_pd', 7)->nullable();
            $table->string('re_int_add', 7)->nullable();
            $table->string('le_sph', 7)->nullable();
            $table->string('le_cyl', 7)->nullable();
            $table->string('le_axis', 7)->nullable();
            $table->string('le_add', 7)->nullable();
            $table->string('le_va', 7)->nullable();
            $table->string('le_pd', 7)->nullable();
            $table->string('le_int_add', 7)->nullable();
            $table->string('a_size', 3)->nullable();
            $table->string('b_size', 3)->nullable();
            $table->string('dbl', 3)->nullable();
            $table->string('fh', 3)->nullable();
            $table->string('ed', 3)->nullable();
            $table->string('vd', 3)->nullable();
            $table->string('w_angle', 3)->nullable();
            $table->unsignedBigInteger('doctor')->nullable();
            $table->unsignedBigInteger('optometrist')->nullable();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('order_id')->nullable();
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
        Schema::dropIfExists('spectacles');
    }
};
