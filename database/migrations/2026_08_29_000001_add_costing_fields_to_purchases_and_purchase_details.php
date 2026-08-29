<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('purchases', 'other_charges')) {
            Schema::table('purchases', function (Blueprint $table) {
                $table->decimal('other_charges', 10, 2)->default(0)->after('purchase_note');
            });
        }

        if (!Schema::hasColumn('purchases', 'other_charges_desc')) {
            Schema::table('purchases', function (Blueprint $table) {
                $table->string('other_charges_desc')->nullable()->after('other_charges');
            });
        }

        if (!Schema::hasColumn('purchases', 'adjust_type')) {
            Schema::table('purchases', function (Blueprint $table) {
                $table->enum('adjust_type', ['plus', 'minus'])->nullable()->after('other_charges_desc');
            });
        }

        if (!Schema::hasColumn('purchases', 'adjust_amount')) {
            Schema::table('purchases', function (Blueprint $table) {
                $table->decimal('adjust_amount', 10, 2)->default(0)->after('adjust_type');
            });
        }

        if (!Schema::hasColumn('purchases', 'adjust_desc')) {
            Schema::table('purchases', function (Blueprint $table) {
                $table->string('adjust_desc')->nullable()->after('adjust_amount');
            });
        }

        if (!Schema::hasColumn('purchase_details', 'discount')) {
            Schema::table('purchase_details', function (Blueprint $table) {
                $table->decimal('discount', 10, 2)->default(0)->after('unit_price_sales');
            });
        }

        if (!Schema::hasColumn('purchase_details', 'tax_percentage')) {
            Schema::table('purchase_details', function (Blueprint $table) {
                $table->decimal('tax_percentage', 5, 2)->default(0)->after('discount');
            });
        }

        if (!Schema::hasColumn('purchase_details', 'tax_amount')) {
            Schema::table('purchase_details', function (Blueprint $table) {
                $table->decimal('tax_amount', 10, 2)->default(0)->after('tax_percentage');
            });
        }
    }

    public function down(): void
    {
        // This compatibility migration may run against databases where some of
        // these columns already existed. Keep rollback non-destructive so it
        // never removes pre-existing frame-purchase data or schema.
    }
};
