<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Earlier development builds linked individual frames. Collection-based
        // eligibility replaces that design, so safely remove the unused table.
        Schema::dropIfExists('offer_product_frames');
    }

    public function down(): void
    {
        // Intentionally non-destructive. Individual frame links are obsolete.
    }
};
