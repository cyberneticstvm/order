<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductCompareExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data->map(function ($data, $key) {
            return [
                'item_seral' => $key + 1,
                'item_name' => $data['product_name'],
                'item_code' => $data['product_code'],
                'stock_in_hand' => $data['stock_in_hand'],
                'uploaded_qty' => $data['uploaded_qty'],
                'difference' => $data['difference'],
            ];
        });
    }

    public function headings(): array
    {
        return ['SL No', 'Product Name', 'Product Code', 'Stock in Hand', 'Uploaded Qty', 'Difference'];
    }

    public function map($data): array
    {
        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
    }
}
