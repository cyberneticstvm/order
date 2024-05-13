<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\StockCompareTemp;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ProductCompareImport implements WithStartRow, ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public $data, $pdct;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function model(array $row)
    {
        $product = Product::where('code', strval($row[0]))->where('category', $this->data->category)->first();
        if ($product) :
            StockCompareTemp::create([
                'branch_id' => $this->data->branch,
                'product_id' => $product->id,
                'product_code' => $product->code,
                'product_name' => $product->name,
                'qty' => $row[3],
                'category' => $this->data->category,
            ]);
        else :
            $this->pdct[] = [
                'product_code' => $row[0],
                'qty' => $row[1],
                'remarks' => 'Product not found',
            ];
        endif;
    }

    public function startRow(): int
    {
        return 2;
    }
}
