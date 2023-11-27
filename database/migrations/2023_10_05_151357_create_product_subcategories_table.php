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
        Schema::create('product_subcategories', function (Blueprint $table) {
            $table->id();            
            $table->enum('category', ['lens', 'frame', 'pharmacy', 'service', 'other']);
            $table->string('attribute')->comment('Like type, shape, coating')->nullable();
            $table->string('name')->comment('Like rim, semirim, round, square, clarity, clarity blue')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_subcategories');
    }
};
