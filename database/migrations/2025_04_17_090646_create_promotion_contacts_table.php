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
        Schema::create('promotion_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('contact_number', 15)->unique()->nullable();
            $table->enum('type', ['include', 'exclude']);
            $table->enum('entity', ['hospital', 'store', 'lab']);
            $table->string('sms_status', 5)->nullable();
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
        Schema::dropIfExists('promotion_contacts');
    }
};
