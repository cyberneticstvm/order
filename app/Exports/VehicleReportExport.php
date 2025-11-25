<?php

namespace App\Exports;

use App\Models\Vehicle;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VehicleReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $request = $this->request;
        dd($rrequest);
        die;
        $vehicles = Vehicle::whereBetween('created_at', [Carbon::parse($request->from_date)->startOfDay(), Carbon::parse($request->to_date)->endOfDay()])->when($request->branch > 0, function ($q) use ($request) {
            return $q->where('branch_id', $request->branch);
        })->get();
        return $vehicles->map(function ($data, $key) {
            return [
                'item_serial' =>  $key + 1,
                'Name' => $data->owner_name,
                'Contact Number' => $data->contact_number,
                'Branch' => $data->branch?->name,
                'Stand' => $data->place,
                'Reg. No.' => $data->reg_number,
                'Status' => $data->isVehicleActive() ? 'Active' : 'Inactive',
                'Days Left' => $data->daysLeft(),
                'Reg. Date' => $data->created_at->format('d.M.Y'),
            ];
        });
    }

    public function headings(): array
    {
        return ['SL No', 'Name', 'Contact Number', 'Branch', 'Stand', 'Reg. No.', 'Status', 'Days Left', 'Reg. Date'];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);
    }
}
