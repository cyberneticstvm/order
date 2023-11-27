<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductPharmacyExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize, WithStyles
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
        $products = Product::with('manufacturer')->where('category', 'pharmacy')->orderBy('name')->get();

        return $products->map(function ($data, $key) {
            return [
                'item_serial' =>  $key + 1,
                'item_name' => $data->name,
                'item_code' => $data->code,
                'item_type' => $data->type?->name,
                'item_manufacturer' => $data->manufacturer?->name,
                'item_mrp' => $data->mrp,
                'item_sp' => $data->selling_price,
                'item_tax' => $data->tax_percentage,
                'item_re' => $data->reorder_level,
                'item_desc' => $data->description,
            ];
        });
    }

    public function headings(): array
    {
        return ['SL No', 'Product Name', 'Product Code', 'Product Type', 'Manufacturer', 'MRP', 'Selling Price', 'Tax %', 'Reorder Level', 'Description'];
    }

    public function map($data): array
    {
        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);
    }
}
