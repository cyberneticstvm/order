<?php

namespace App\Exports;

use App\Models\Product;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockStatusReportExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $request = $this->request;
        $stock = getInventory($request->branch, 0, $request->category);
        return $stock->map(function ($data, $key) {
            return [
                'item_serial' =>  $key + 1,
                'product_name' => $data->name,
                'product_code' => $data->pcode,
                'booked' => $data->soldQty,
                'billed' => $data->billedQty,
                'transfer_in' => $data->purchasedQty,
                'transfer_out' => $data->transferredQty,
                'returned' => $data->returnedQty,
                'damaged' => $data->damagedQty,
                'balance' => $data->balanceQty,
                'shelf' => $data->shelfQty,
                'mrp' => Product::find($data->id)->mrp,
                'selling_price' => Product::find($data->id)->selling_price,
            ];
        });
    }

    public function headings(): array
    {
        return ['SL No', 'Product Name', 'Code', 'Booked', 'Billed', 'Transfer In', 'Transfer Out', 'Returned', 'Damaged', 'Balance', 'Shelf', 'MRP', 'Selling Price'];
    }

    public function map($data): array
    {
        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:M1')->getFont()->setBold(true);
    }
}
