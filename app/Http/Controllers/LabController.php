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
        $this->middleware('permission:received-from-lab-orders', ['only' => ['receivedFromLab']]);
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
        })->whereNotIn('o.order_status', ['delivered', 'ready-for-delivery'])->whereNull('o.deleted_at')->whereNull("lo.lab_id")->union($ord)->get();
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
            LabOrder::where('order_detail_id', $odetail->id)->delete();
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
        $orders = LabOrder::whereIn('status', ['sent-to-lab', 'job-completed', 'job-under-process'])->when(!in_array(Auth::user()->roles->first()->name, array('Administrator', 'Store Manager', 'CEO')), function ($q) {
            return $q->where('lab_id', Session::get('branch'));
        })->get()->unique('order_detail_id');
        /*when(!in_array(Auth::user()->roles->first()->name, array('Administrator', 'Store Manager')), function ($q) {
            return $q->where('lab_id', Session::get('branch'));
        })->*/

        if (in_array(Auth::user()->roles->first()->name, array('Store Manager', 'Administrator', 'CEO'))) :
            $status = array('received-from-lab' => 'Received From Lab', 'sent-to-branch' => 'Sent to Branch', 'sent-to-lab' => 'Sent to Lab', 'sent-to-main-branch' => 'Sent to Main Branch', 'job-completed' => 'Job Completed', 'job-under-process' => 'Job Under Process');
            $labs = Branch::whereIn('type', ['rx-lab', 'fitting-lab', 'stock-lab', 'outside-lab'])->selectRaw("id, name")->get();
        else :
            $status = array('sent-to-branch' => 'Sent to Origin Branch', 'sent-to-main-branch' => 'Sent to Main Branch', 'job-completed' => 'Job Completed', 'job-under-process' => 'Job Under Process');
            $labs = collect();
        endif;
        //$br = Branch::selectRaw("0 as id, 'Main Branch' as name");

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
            elseif ($request->status == 'job-completed' || $request->status == 'job-under-process') :
                LabOrder::whereIn('id', $request->chkItem)->update([
                    'status' => $request->status,
                    'updated_by' => $request->user()->id,
                ]);
            elseif ($request->status == 'sent-to-branch' || $request->status == 'sent-to-main-branch') :
                foreach ($request->chkItem as $key => $item) :
                    $lab = LabOrder::findOrFail($item);
                    $lab->update([
                        'status' => $request->status,
                        'lab_id' => ($request->status == 'sent-to-main-branch') ? 0 : $lab->getOriginal('lab_id'),
                        'updated_by' => $request->user()->id,
                    ]);
                endforeach;
            else :
                LabOrder::whereIn('id', $request->chkItem)->update([
                    'status' => $request->status,
                    'updated_by' => $request->user()->id,
                ]);
            endif;
            foreach ($request->chkItem as $key => $item) :
                $lab = LabOrder::findOrFail($item);
                $odetail = OrderDetail::findOrFail($lab->order_detail_id);
                $action = "";
                $lname = Branch::where('id', ($request->status == 'sent-to-main-branch') ? 0 : $odetail->order->branch_id)->first()?->name ?? 'Main Branch';
                if ($request->status == 'sent-to-branch') :
                    $action = 'Order has been sent back to ' . $lname . ' - ' . strtoupper($odetail->eye);
                endif;
                if ($request->status == 'sent-to-main-branch') :
                    $action = 'Order has been sent to Main Brnach - ' . strtoupper($odetail->eye);
                endif;
                if ($request->status == 'sent-to-lab') :
                    $action  = 'Order has transferred to ' . Branch::where('id', ($request->lab_id) ?? $lab->getOriginal('lab_id'))->first()?->name ?? 'Main Branch' . ' - ' . strtoupper($odetail->eye);
                endif;
                if ($request->status == 'received-from-lab') :
                    $action  = "Order has received from lab ($lname) " . strtoupper($odetail->eye);
                endif;
                if ($request->status == 'job-completed' || $request->status == 'job-under-process') :
                    $action = "Order marked as " . $request->status . ' - ' . strtoupper($odetail->eye);
                endif;
                $data[] = [
                    'order_id' => $odetail->order->id,
                    'action' => $action,
                    'performed_by' => $request->user()->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            endforeach;
            OrderHistory::insert($data);
        });
        return redirect()->route('lab.view.orders')->with("success", "Status updated successfully");
    }

    public function receivedFromLab()
    {
        $orders = LabOrder::whereIn('status', ['received-from-lab'])->when(!in_array(Auth::user()->roles->first()->name, array('Administrator', 'Store Manager', 'CEO')), function ($q) {
            return $q->where('lab_id', Session::get('branch'));
        })->get()->unique('order_detail_id');

        $status = array('sent-to-branch' => 'Sent to Branch', 'sent-to-lab' => 'Sent to Lab', 'sent-to-main-branch' => 'Sent to Main Branch');

        $labs = Branch::whereIn('type', ['stock-lab', 'fitting-lab'])->selectRaw("id, name")->get();
        return view('backend.lab.received-from-lab', compact('orders', 'status', 'labs'));
    }

    public function receivedFromLabUpdate(Request $request)
    {
        $this->validate($request, [
            'status' => 'required',
        ]);
        DB::transaction(function () use ($request) {
            $data = [];
            if ($request->status == 'sent-to-lab' && $request->lab_id) :
                LabOrder::whereIn('id', $request->chkItem)->update([
                    'status' => $request->status,
                    'lab_id' => $request->lab_id,
                    'updated_by' => $request->user()->id,
                ]);
            elseif ($request->status == 'sent-to-branch' || $request->status == 'sent-to-main-branch') :
                foreach ($request->chkItem as $key => $item) :
                    $lab = LabOrder::findOrFail($item);
                    $lab->update([
                        'status' => $request->status,
                        'lab_id' => ($request->status == 'sent-to-main-branch') ? 0 : $lab->getOriginal('lab_id'),
                        'updated_by' => $request->user()->id,
                    ]);
                endforeach;
            endif;
            foreach ($request->chkItem as $key => $item) :
                $lab = LabOrder::findOrFail($item);
                $odetail = OrderDetail::findOrFail($lab->order_detail_id);
                $action = "";
                if ($request->status == 'sent-to-lab' && $request->lab_id) :
                    $action = 'Order has transferred to ' . Branch::where('id', ($request->lab_id) ?? $lab->getOriginal('lab_id'))->first()?->name ?? 'Main Branch' . ' - ' . strtoupper($odetail->eye);
                endif;
                if ($request->status == 'sent-to-main-branch') :
                    $action = 'Order has transferred to Main Branch' . ' - ' . strtoupper($odetail->eye);
                endif;
                if ($request->status == 'sent-to-branch') :
                    $action = 'Order has been sent back to Branch' . strtoupper($odetail->eye);
                endif;
                $data[] = [
                    'order_id' => $odetail->order->id,
                    'action' => $action,
                    'performed_by' => $request->user()->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            endforeach;
            OrderHistory::insert($data);
        });
        return redirect()->back()->with("success", "Status updated successfully");
    }

    public function delete(string $id)
    {
        LabOrder::findOrFail(decrypt($id))->delete();
        return redirect()->back()->with("success", "Status deleted successfully");
    }
}
