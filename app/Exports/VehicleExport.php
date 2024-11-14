<?php

namespace App\Exports;

use App\Models\Vehicle;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VehicleExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $products = Vehicle::withTrashed()->orderBy('owner_name')->get();
        return $products->map(function ($data, $key) {
            return [
                'item_serial' =>  $key + 1,
                'owner_name' => $data->owner_name,
                'vehicle_number' => $data->reg_number,
                'vehicle_code' => $data->vcode,
                'stand_name' => $data->place,
                'active_status' => $data->isVehicleActive() ? 'Yes' : 'No',
                'card_issued' => $data->card_issued == 1 ? 'Yes' : 'No',
            ];
        });
    }

    public function headings(): array
    {
        return ['SL No', 'Owner Name', 'Vehicle Number', 'Vehicle Code', 'Stand Name', 'Active Status', 'Card Issued'];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
    }
}
