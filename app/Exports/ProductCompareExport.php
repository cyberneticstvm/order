<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
/*use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Conditional\Rule;
use Maatwebsite\Excel\Concerns\WithPreCalculateFormulas;*/

class ProductCompareExport implements WithEvents, FromCollection, WithMapping, WithHeadings, ShouldAutoSize, WithStyles
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
                'stock_in_hand' => $data['stock_in_hand'] ?? 0,
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
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $numOfRows = count($this->data) + 1;
        $totalRow = $numOfRows + 2;
        $sheet->setCellValue("E{$totalRow}", "Total");
        $sheet->setCellValue("F{$totalRow}", "=SUM(F1:F{$numOfRows})");
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Get the total number of rows
                $totalRows = count($this->data);

                // Loop through each row and apply conditional formatting
                for ($row = 2; $row <= $totalRows + 1; $row++) {
                    $cellValue = $event->sheet->getCell('F' . $row)->getValue();

                    if ($cellValue > 0) {
                        // Apply bold font to cell A in the current row
                        $event->sheet->getDelegate()->getStyle("A$row:F$row")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setARGB('ffd22b');
                    }
                    if ($cellValue < 0) {
                        // Apply bold font to cell A in the current row
                        $event->sheet->getDelegate()->getStyle("A$row:F$row")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setARGB('d53600');
                    }
                }
            },
        ];
    }
}
