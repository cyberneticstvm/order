<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\PurchaseDetail;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ProductPurchaseImport implements ToModel, WithStartRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public $purchase;
    public function __construct($purchase)
    {
        $this->purchase = $purchase;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function model(array $row)
    {
        return new PurchaseDetail([
            'purchase_id' => $this->purchase->id,
            'product_id' => Product::where('code', strval($row[1]))->where('category', 'frame')->first()->id,
            'qty' => $row[2],
            'unit_price_mrp' => $row[3],
            'unit_price_purchase' => $row[4],
            'unit_price_sales' => $row[5],
            'total' => floatval($row[2]) * floatval($row[4]),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

        ]);
    }
}
