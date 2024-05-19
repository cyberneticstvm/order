<?php

namespace App\Exports;

use App\Models\Order;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrderExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize, WithStyles
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
        $sales = Order::whereBetween(($request->status != 'delivered') ? 'order_date' : 'invoice_generated_at', [Carbon::parse($request->fdate)->startOfDay(), Carbon::parse($request->tdate)->endOfDay()])->when($request->branch > 0, function ($q) use ($request) {
            return $q->where('branch_id', $request->branch);
        })->when($request->status != 'all', function ($q) use ($request) {
            return $q->where('order_status', $request->status);
        })->orderByDesc('created_at')->get();

        return $sales->map(function ($data, $key) {
            return [
                'item_serial' =>  $key + 1,
                'order_no' => $data->ono(),
                'bill_no' => $data->ino(),
                'branch' => $data->branch->name,
                'order_date' => $data->created_at->format('d.M.Y'),
                'order_status' => $data->order_status,
                'advance' => $data->advance,
                'balance' => $data->balance,
                'order_total' => $data->invoice_total,
            ];
        });
    }

    public function headings(): array
    {
        return ['SL No', 'Order No.', 'Bill No.', 'Branch', 'Order Date', 'Order Status', 'Advance', 'Balance', 'Order Total'];
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
