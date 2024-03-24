<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Product;
use App\Models\PurchaseDetail;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ProductLensPurchaseImport implements WithStartRow, ToCollection
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public $purchase, $data;

    public function __construct($purchase)
    {
        $this->purchase = $purchase;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $key => $row) :
            $product = Product::where('code', strval($row[1]))->where('category', 'lens')->first();
            if ($product) :
                PurchaseDetail::create([
                    'purchase_id' => $this->purchase->id,
                    'product_id' => $product->id,
                    'qty' => $row[2],
                    'unit_price_mrp' => $row[3],
                    'unit_price_purchase' => $row[4],
                    'unit_price_sales' => $row[5],
                    'total' => floatval($row[2]) * floatval($row[4]),
                ]);
            else :
                $this->data[] = [
                    'product_code' => $row[1],
                ];
            endif;
        endforeach;
    }

    /*public function model(array $row)
    {
        $product = Product::where('code', strval($row[1]))->where('category', 'lens')->first();
        if ($product) :
            return new PurchaseDetail([
                'purchase_id' => $this->purchase->id,
                'product_id' => $product->id,
                'qty' => $row[2],
                'unit_price_mrp' => $row[3],
                'unit_price_purchase' => $row[4],
                'unit_price_sales' => $row[5],
                'total' => floatval($row[2]) * floatval($row[4]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        else :
            $this->data = [
                'product_id' => $row[1],
            ];
            $this->data;
        endif;
        //return Excel::download(new FailedProductsExport($data), 'products.xlsx');
    }*/
}
