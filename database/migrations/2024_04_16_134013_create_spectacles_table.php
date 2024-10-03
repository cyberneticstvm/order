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
            $table->unsignedBigInteger('registration_id')->nullable();
            $table->string('re_sph', 7)->nullable();
            $table->string('re_cyl', 7)->nullable();
            $table->string('re_axis', 7)->nullable();
            $table->string('re_add', 7)->nullable();
            $table->string('re_va', 7)->nullable();
            $table->string('re_uc', 7)->nullable();
            $table->string('le_sph', 7)->nullable();
            $table->string('le_cyl', 7)->nullable();
            $table->string('le_axis', 7)->nullable();
            $table->string('le_add', 7)->nullable();
            $table->string('le_va', 7)->nullable();
            $table->string('le_uc', 7)->nullable();
            $table->string('vd', 7)->nullable();
            $table->string('ipd', 7)->nullable();
            $table->string('npd', 7)->nullable();
            $table->string('rpd', 7)->nullable();
            $table->string('lpd', 7)->nullable();
            $table->string('arm_od_sph', 7)->nullable();
            $table->string('arm_od_cyl', 7)->nullable();
            $table->string('arm_od_axis', 7)->nullable();
            $table->string('arm_os_sph', 7)->nullable();
            $table->string('arm_os_cyl', 7)->nullable();
            $table->string('arm_os_axis', 7)->nullable();
            $table->string('pgp_od_sph', 7)->nullable();
            $table->string('pgp_od_cyl', 7)->nullable();
            $table->string('pgp_od_axis', 7)->nullable();
            $table->string('pgp_od_add', 7)->nullable();
            $table->string('pgp_od_vision', 7)->nullable();
            $table->string('pgp_od_nv', 7)->nullable();
            $table->string('pgp_os_sph', 7)->nullable();
            $table->string('pgp_os_cyl', 7)->nullable();
            $table->string('pgp_os_axis', 7)->nullable();
            $table->string('pgp_os_add', 7)->nullable();
            $table->string('pgp_os_vision', 7)->nullable();
            $table->string('pgp_os_nv', 7)->nullable();
            $table->string('cl_od_base_curve', 7)->nullable();
            $table->string('cl_od_dia', 7)->nullable();
            $table->string('cl_od_sph', 7)->nullable();
            $table->string('cl_od_cyl', 7)->nullable();
            $table->string('cl_od_axis', 7)->nullable();
            $table->string('cl_os_base_curve', 7)->nullable();
            $table->string('cl_os_dia', 7)->nullable();
            $table->string('cl_os_sph', 7)->nullable();
            $table->string('cl_os_cyl', 7)->nullable();
            $table->string('cl_os_axis', 7)->nullable();
            $table->unsignedBigInteger('doctor')->nullable();
            $table->unsignedBigInteger('optometrist')->nullable();
            $table->unsignedBigInteger('optometrist_hospital')->nullable();
            $table->text('notes')->nullable();
            $table->text('advice')->nullable();
            $table->unsignedBigInteger('branch_id');
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
