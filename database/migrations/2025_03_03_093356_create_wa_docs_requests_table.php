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
        Schema::create('wa_docs_requests', function (Blueprint $table) {
            $table->id();
            $table->string('doc_type');
            $table->unsignedBigInteger('doc_id');
            $table->string('sent_to', 10);
            $table->unsignedBigInteger('sent_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_docs_requests');
    }
};
