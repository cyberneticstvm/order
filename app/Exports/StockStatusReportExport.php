<?php

namespace App\Exports;

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
    protected $category, $branch;

    public function __construct($category, $branch)
    {
        $this->category = $category;
        $this->branch = $branch;
    }

    public function collection()
    {
        $category = $this->category;
        $branch = $this->branch;
        echo $category;
        die;
        $stock = getInventory($category, 0, $branch);
        return $stock->map(function ($data, $key) {
            return [
                'item_serial' =>  $key + 1,
                'product_name' => $data->product_name,
                'product_code' => $data->pcode,
                'booked' => $data->soldQty,
                'billed' => $data->billedQty,
                'transfer_in' => $data->purchasedQty,
                'transfer_out' => $data->transferredQty,
                'returned' => $data->returnedQty,
                'damaged' => $data->damagedQty,
                'balance' => $data->balanceQty,
            ];
        });
    }

    public function headings(): array
    {
        return ['SL No', 'Product Name', 'Code', 'Booked', 'Billed', 'Transfer In', 'Transfer Out', 'Returned', 'Damaged', 'Balance'];
    }

    public function map($data): array
    {
        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);
    }
}
