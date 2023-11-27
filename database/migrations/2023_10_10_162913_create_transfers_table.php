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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_number')->unique();
            $table->enum('category', ['lens', 'frame', 'pharmacy', 'service', 'other'])->nullable();
            $table->unsignedBigInteger('from_branch_id');
            $table->unsignedBigInteger('to_branch_id');
            $table->date('transfer_date')->nullable();
            $table->text('transfer_note')->nullable();
            $table->boolean('transfer_status')->comment('1-accepted, 0-pending')->default('0');
            $table->unsignedBigInteger('accepted_by')->nullable();
            $table->datetime('accepted_at')->nullable();
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('transfers');
    }
};
