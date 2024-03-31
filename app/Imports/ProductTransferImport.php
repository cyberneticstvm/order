<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\TransferDetails;
use Carbon\Carbon;
use Exception;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ProductTransferImport implements ToModel, WithStartRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public $data, $transfer;

    public function __construct($transfer)
    {
        $this->transfer = $transfer;
    }

    public function model(array $row)
    {
        try {
            $product = Product::where('code', strval($row[1]))->first();
            if ($product) :
                return new TransferDetails([
                    'transfer_id' => $this->transfer->id,
                    'product_id' => $product->id,
                    'qty' => $row[2],
                    'batch_number' => $row[3],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
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
