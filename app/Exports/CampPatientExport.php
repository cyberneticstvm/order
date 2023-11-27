<?php

namespace App\Exports;

use App\Models\Camp;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CampPatientExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    protected $request, $id;

    public function __construct($request, $id)
    {
        $this->request = $request;
        $this->id = $id;
    }

    public function collection()
    {
        $camp = Camp::findOrFail(decrypt($this->id));
        return $camp->patients->map(function($data, $key) use($camp) {
            return [
                'item_serial' =>  $key + 1,
                'item_name' => $data->name,
                'item_age'=> $data->age,
                'item_gender' => $data->gender,
                'item_place' => $data->place,
                'item_mobile' => $data->mobile,
                'item_camp_name' => $camp->name,
            ];
        });
    }

    public function headings(): array {
        return ['SL No', 'Patient Name', 'Age', 'Gender', 'Place', 'Mobile', 'Camp Name'];
    }

    public function map($data): array {
        return $data;
    }

    public function styles(Worksheet $sheet){
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
    }
}
