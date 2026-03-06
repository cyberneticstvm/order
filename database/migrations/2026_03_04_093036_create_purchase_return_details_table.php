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
        Schema::create('purchase_return_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId("return_id")->constrained("purchase_returns", "id")->onDelete("cascade");
            $table->foreignId("supplier_id")->constrained()->onDelete("cascade");
            $table->foreignId("product_id")->onDelete("cascade");
            $table->integer("qty")->default(0);
            $table->decimal("price", 8, 2)->default(0);
            $table->decimal("total", 8, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_return_details');
    }
};
