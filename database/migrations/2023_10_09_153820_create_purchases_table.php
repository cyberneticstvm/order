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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->enum('category', ['lens', 'frame', 'pharmacy', 'service', 'other']);
            $table->string('purchase_number')->unique();
            $table->unsignedBigInteger('supplier_id');
            $table->date('order_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->string('purchase_invoice_number')->nullable();
            $table->text('purchase_note')->nullable();
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
        Schema::dropIfExists('purchases');
    }
};
