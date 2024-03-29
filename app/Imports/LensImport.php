<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductSubcategory;
use Exception;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class LensImport implements ToModel, WithStartRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public $data;

    public function model(array $row)
    {
        try {
            $product = Product::where('code', strval($row[1]))->first();
            $type = ProductSubcategory::where('category', 'lens')->where('name', $row[2])->where('attribute', 'type')->first();
            $material = ProductSubcategory::where('category', 'lens')->where('name', $row[3])->where('attribute', 'material')->first();
            $coating = ProductSubcategory::where('category', 'lens')->where('name', $row[4])->where('attribute', 'colour')->first();
            if ((!$product) && $type && $material && $coating) :
                return new Product([
                    'name' => $row[0],
                    'code' => $row[1],
                    'category' => 'lens',
                    'type_id' => $type->id,
                    'material' => $material->id,
                    'coating_id' => $coating->id,
                    'manufacturer_id' => null,
                    'reorder_level' => $row[9],
                    'tax_percentage' => $row[8],
                    'mrp' => $row[6],
                    'selling_price' => $row[7],
                    'description' => $row[10],
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            else :
                $this->data[] = [
                    'Product_Name' => $row[0],
                    'Product_Code' => $row[1],
                ];
            endif;
        } catch (Exception $e) {
            return back()->with("error", $e->getMessage());
        }
    }

    public function startRow(): int
    {
        return 2;
    }
}
