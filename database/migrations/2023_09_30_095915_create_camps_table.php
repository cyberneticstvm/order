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
        Schema::create('camps', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('camp_id', 15)->unique();
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->string('venue', 125)->nullable();
            $table->string('address')->nullable();
            $table->unsignedBigInteger('cordinator')->nullable();
            $table->unsignedBigInteger('camp_type')->nullable();
            $table->unsignedBigInteger('optometrist')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->foreign('cordinator')->references('id')->on('users');
            $table->foreign('optometrist')->references('id')->on('users');
            $table->foreign('camp_type')->references('id')->on('camp_types');
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camps');
    }
};
