<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->decimal('other_charges', 10, 2)->default(0)->after('purchase_note');
            $table->string('other_charges_desc')->nullable()->after('other_charges');
            $table->enum('adjust_type', ['plus', 'minus'])->nullable()->after('other_charges_desc');
            $table->decimal('adjust_amount', 10, 2)->default(0)->after('adjust_type');
            $table->string('adjust_desc')->nullable()->after('adjust_amount');
        });

        Schema::table('purchase_details', function (Blueprint $table) {
            $table->decimal('discount', 10, 2)->default(0)->after('unit_price_sales');
            $table->decimal('tax_percentage', 5, 2)->default(0)->after('discount');
            $table->decimal('tax_amount', 10, 2)->default(0)->after('tax_percentage');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_details', function (Blueprint $table) {
            $table->dropColumn(['discount', 'tax_percentage', 'tax_amount']);
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn([
                'other_charges',
                'other_charges_desc',
                'adjust_type',
                'adjust_amount',
                'adjust_desc',
            ]);
        });
    }
};
