<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductSubcategory;
use Exception;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class FrameImport implements ToModel, WithStartRow
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
            $type = ProductSubcategory::where('category', 'frame')->where('name', $row[2])->where('attribute', 'type')->first();
            $shape = ProductSubcategory::where('category', 'frame')->where('name', $row[3])->where('attribute', 'shape')->first();
            $material = ProductSubcategory::where('category', 'frame')->where('name', $row[4])->where('attribute', 'material')->first();
            $color = ProductSubcategory::where('category', 'frame')->where('name', $row[14])->where('attribute', 'colour')->first();
            $collection = ProductSubcategory::where('category', 'frame')->where('name', $row[13])->where('attribute', 'collection')->first();
            if ((!$product) && $type && $shape && $material && $color && $collection) :
                $pdct = new Product([
                    'name' => $row[0],
                    'code' => $row[1],
                    'category' => 'frame',
                    'type_id' => $type->id,
                    'shape_id' => $shape->id,
                    'material' => $material->id,
                    'manufacturer_id' => null,
                    'color' => $color->id,
                    'reorder_level' => $row[9],
                    'tax_percentage' => $row[8],
                    'mrp' => $row[6],
                    'selling_price' => $row[7],
                    'eye_size' => $row[10],
                    'bridge_size' => $row[11],
                    'temple_size' => $row[12],
                    'description' => $row[15],
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
                if (getSubDomain() == 'store')
                    addProductToSASStore($product);
                return $pdct;
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
