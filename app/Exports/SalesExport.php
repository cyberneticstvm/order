<?php

namespace App\Exports;

use App\Models\Order;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;

class SalesExport implements FromCollection
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
        $sales = Order::whereBetween(($request->order_status != 'delivered') ? 'order_date' : 'invoice_generated_at', [Carbon::parse($request->from_date)->startOfDay(), Carbon::parse($request->to_date)->endOfDay()])->when($request->branch > 0, function ($q) use ($request) {
            return $q->where('branch_id', $request->branch);
        })->when($request->order_status != 'all', function ($q) use ($request) {
            return $q->where('order_status', $request->order_status);
        })->orderBy('order_sequence', 'ASC')->get();

        dd($this->request);
        die;

        return $sales->map(function ($data, $key) {
            return [
                'item_serial' =>  $key + 1,
                'bill_no' => $data->ino(),
                'date' => $data->invoice_generated_at,
                'gstin' => '',
                'customer' => $data->name,
                'type' => 'Sales',
                'cgst' => $data->details()->sum('tax_amount') / 2,
                'igst' => $data->details()->sum('tax_amount') / 2,
                'net_amount' => $data->order_total,
                'invoice_amount' => $data->invoice_total,
                'remarks' => '',
                'state' => 'Kerala',
                'registration' => 'Regular',
                'place' => 'Kerala',
            ];
        });
    }
}
