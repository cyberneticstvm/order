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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vcode', 15)->unique()->nullable();
            $table->string('vehicle_id', 5)->unique()->nullable();
            $table->string('owner_name', 100);
            $table->string('reg_number', 15)->unique();
            $table->string('place', 100)->nullable();
            $table->string('contact_number', 15);
            $table->decimal('fee', 5, 2)->default(0);
            $table->integer('payment_terms')->comment('in days')->default(0);
            $table->string('upi_id', 50)->nullable();
            $table->boolean('card_issued')->default(0);
            $table->date('card_issued_date')->nullable();
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
        Schema::dropIfExists('vehicles');
    }
};
