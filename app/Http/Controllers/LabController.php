<?php

namespace App\Http\Controllers;

use App\Mail\SendOrderToLab;
use App\Models\Branch;
use App\Models\Lab;
use App\Models\LabOrder;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class LabController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->middleware('permission:lab-list|lab-create|lab-edit|lab-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:lab-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:lab-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:lab-delete', ['only' => ['destroy']]);
        $this->middleware('permission:lab-assign-orders', ['only' => ['assignOrders']]);
        $this->middleware('permission:lab-assigned-orders', ['only' => ['labOrders']]);
        $this->middleware('permission:lab-assigned-order-delete', ['only' => ['delete']]);
    }

    public function index()
    {
        $labs = Lab::withTrashed()->get();
        return view('backend.lab.index', compact('labs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.lab.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:labs,name',
        ]);
        Lab::create([
            'name' => $request->name,
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);
        return redirect()->route('labs')->with("success", "Lab created successfully");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $lab = Lab::findOrFail(decrypt($id));
        return view('backend.lab.edit', compact('lab'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'name' => 'required|unique:labs,name,' . $id,
        ]);
        Lab::findOrFail($id)->update([
            'name' => $request->name,
        ]);
        return redirect()->route('labs')->with("success", "Lab created successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Lab::findOrFail(decrypt($id))->delete();
        return redirect()->route('labs')->with("success", "Lab deleted successfully");
    }

    public function assignOrders()
    {
        $ord = OrderDetail::whereIn('id', LabOrder::where('lab_id', 0)->pluck('order_detail_id'))->selectRaw("order_details.*, '0' as lab_id");
        $orders = OrderDetail::leftJoin("lab_orders as lo", "lo.order_detail_id", "order_details.id")->leftJoin('orders as o', 'o.id', 'order_details.order_id')->selectRaw("order_details.*, lo.lab_id")->whereIn('order_details.eye', ['re', 'le'])->when(!in_array(Auth::user()->roles->first()->name, array('Administrator', 'CEO')), function ($q) {
            return $q->where('o.branch_id', Session::get('branch'));
        })->whereNotIn('o.order_status', ['delivered', 'ready-for-delivery'])->whereNull('o.deleted_at')->whereNull("lo.lab_id")->union($ord)->unique('lo.order_detail_id');
        $labs = Branch::whereIn('type', ['rx-lab', 'fitting-lab', 'stock-lab', 'outside-lab'])->get();
        return view('backend.lab.orders', compact('orders', 'labs'));
    }

    public function assignOrdersSave(Request $request)
    {
        $this->validate($request, [
            'lab_id' => 'required',
        ]);
        $lab = Branch::findOrFail($request->lab_id);
        $data = [];
        $data1 = [];
        $data2 = [];
        foreach ($request->chkItem as $key => $item) :
            $odetail = OrderDetail::findOrFail($item);
            $data[] = [
                'order_id' => $odetail->order->id,
                'order_detail_id' => $odetail->id,
                'lab_id' => $request->lab_id,
                'status' => 'sent-to-lab',
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            $data1[] = collect([
                'order_id' => $odetail->order->ono(),
                'eye' => $odetail->eye,
                'product' => $odetail->product->name,
                'qty' => $odetail->qty,
                'sph' => $odetail->sph,
                'cyl' => $odetail->cyl,
                'axis' => $odetail->axis,
                'add' => $odetail->add,
                'ipd' => $odetail->ipd,
                'a_size' => $odetail->order->a_size,
                'b_size' => $odetail->order->b_size,
                'dbl' => $odetail->order->dbl,
                'fh' => $odetail->order->fh,
                'ed' => $odetail->order->ed,
                'special_lab_note' => $odetail->order->special_lab_note,
                'frame_type' => getFrameType($odetail->order->id),
                'customer' => $odetail->order->name,
            ]);
            $data2[] = [
                'order_id' => $odetail->order->id,
                'action' => 'Order has been assigned to ' . Branch::find($request->lab_id)->name . ' - ' . strtoupper($odetail->eye),
                'performed_by' => $request->user()->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        endforeach;
        DB::transaction(function () use ($data, $data2) {
            LabOrder::insert($data);
            OrderHistory::insert($data2);
        });
        if ($lab->type == 'outside-lab') :
            Mail::to($lab->email)->cc('cssumesh@yahoo.com')->send(new SendOrderToLab($data1, $lab));
        //Mail::to('mail@cybernetics.me')->cc('vijoysasidharan@yahoo.com')->send(new SendOrderToLab($data1, $lab));
        endif;
        return redirect()->route('lab.assign.orders')->with("success", "Order assigned successfully");
    }

    public function labOrders()
    {
        $orders = LabOrder::whereIn('status', ['sent-to-lab', 'received-from-lab'])->when(!in_array(Auth::user()->roles->first()->name, array('Administrator', 'Store Manager', 'CEO')), function ($q) {
            return $q->where('lab_id', Session::get('branch'));
        })->get();
        /*when(!in_array(Auth::user()->roles->first()->name, array('Administrator', 'Store Manager')), function ($q) {
            return $q->where('lab_id', Session::get('branch'));
        })->*/

        if (in_array(Auth::user()->roles->first()->name, array('Store Manager', 'Administrator'))) :
            $status = array('received-from-lab' => 'Received From Lab', 'sent-to-branch' => 'Sent to Branch', 'sent-to-lab' => 'Sent to Lab');
        else :
            $status = array('sent-to-branch' => 'Sent to Branch', 'sent-to-lab' => 'Sent to Lab');
        endif;
        $br = Branch::selectRaw("0 as id, 'Main Branch' as name");
        $labs = Branch::whereIn('type', ['rx-lab', 'fitting-lab', 'stock-lab', 'outside-lab'])->selectRaw("id, name")->union($br)->get();
        return view('backend.lab.lab-orders', compact('orders', 'status', 'labs'));
    }

    public function labOrdersUpdateStatus(Request $request)
    {
        $this->validate($request, [
            'status' => 'required',
        ]);
        DB::transaction(function () use ($request) {
            $data = [];
            if ($request->status == 'sent-to-lab') :
                LabOrder::whereIn('id', $request->chkItem)->update([
                    'status' => $request->status,
                    'lab_id' => $request->lab_id,
                    'updated_by' => $request->user()->id,
                ]);
            else :
                LabOrder::whereIn('id', $request->chkItem)->update([
                    'status' => $request->status,
                    'lab_id' => $request->lab_id,
                    'updated_by' => $request->user()->id,
                ]);
            endif;
            foreach ($request->chkItem as $key => $item) :
                $lab = LabOrder::findOrFail($item);
                $odetail = OrderDetail::findOrFail($lab->order_detail_id);
                $data[] = [
                    'order_id' => $odetail->order->id,
                    'action' => 'Order has been sent back to ' . Branch::where('id', $odetail->order->branch_id)->first()->name . ' - ' . strtoupper($odetail->eye),
                    'performed_by' => $request->user()->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            endforeach;
            OrderHistory::insert($data);
        });
        return redirect()->route('lab.view.orders')->with("success", "Status updated successfully");
    }

    public function delete(string $id)
    {
        LabOrder::findOrFail(decrypt($id))->delete();
        return redirect()->back()->with("success", "Status deleted successfully");
    }
}
