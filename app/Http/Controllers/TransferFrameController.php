<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Transfer;
use App\Models\TransferDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Exception;

class TransferFrameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $products, $transfers, $branches, $tobranches;

    public function __construct()
    {
        $this->middleware('permission:frame-transfer-list|frame-transfer-create|frame-transfer-edit|frame-transfer-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:frame-transfer-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:frame-transfer-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:frame-transfer-delete', ['only' => ['destroy']]);

        $this->middleware(function ($request, $next) {
            $this->transfers = Transfer::when(!in_array(Auth::user()->roles->first()->name, array('Administrator', 'CEO', 'Store Manager')), function ($q) {
                return $q->where('from_branch_id', Session::get('branch'));
            })->where('transfer_status', 0)->where('category', 'frame')->withTrashed()->latest()->get();

            $brs = Branch::where('ho_master', 1)->selectRaw("id, name");
            $this->branches = Branch::selectRaw("id, name")->where('id', Session::get('branch'))->when(in_array(Auth::user()->roles->first()->name, ['Administrator', 'CEO', 'Store Manager', 'Accounts']), function ($q) use ($brs) {
                return $q->union($brs);
            })->orderBy('id')->pluck('name', 'id');

            $this->tobranches = Branch::selectRaw("id, name")->where('id', '<>', Session::get('branch'))->when(in_array(Auth::user()->roles->first()->name, ['Administrator', 'CEO', 'Store Manager', 'Accounts']), function ($q) use ($brs) {
                return $q->union($brs);
            })->orderBy('id')->pluck('name', 'id');

            $this->products = getInventory(Session::get('branch'), 0, 'frame')->where('balanceQty', '>', 0);

            return $next($request);
        });

        //$this->products = Product::whereIn('category', ['frame'])->selectRaw("id, CONCAT_WS('-', name, code) AS name")->orderBy('name')->pluck('name', 'id');
    }

    public function index()
    {
        $transfers = $this->transfers;
        return view('backend.transfer.frame.index', compact('transfers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = $this->products;
        $branches = $this->branches;
        $tobranches = $this->tobranches;
        return view('backend.transfer.frame.create', compact('products', 'branches', 'tobranches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'transfer_date' => 'required',
            'from_branch_id' => 'required',
            'to_branch_id' => 'required',
            'product_id' => 'present|array'
        ]);
        try {
            DB::transaction(function () use ($request) {
                $transfer = Transfer::create([
                    'transfer_number' => transferId('frame')->tid,
                    'category' => 'frame',
                    'transfer_date' => $request->transfer_date,
                    'from_branch_id' => $request->from_branch_id,
                    'to_branch_id' => $request->to_branch_id,
                    'transfer_note' => $request->transfer_note,
                    'transfer_status' => 0,
                    'created_by' => $request->user()->id,
                    'updated_by' => $request->user()->id,
                ]);
                $data = [];
                foreach ($request->product_id as $key => $item) :
                    $data[] = [
                        'transfer_id' => $transfer->id,
                        'product_id' => $item,
                        'qty' => $request->qty[$key],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                endforeach;
                TransferDetails::insert($data);
            });
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage())->withInput($request->all());
        }
        return redirect()->route('frame.transfer')->with("success", "Product transferred successfully!");
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
        $products = $this->products;
        $branches = $this->branches;
        $transfer = Transfer::findOrFail(decrypt($id));
        $tobranches = $this->tobranches;
        return view('backend.transfer.frame.edit', compact('products', 'branches', 'transfer', 'tobranches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'transfer_date' => 'required',
            'from_branch_id' => 'required',
            'to_branch_id' => 'required',
            'product_id' => 'present|array'
        ]);
        try {
            DB::transaction(function () use ($request, $id) {
                Transfer::findOrFail($id)->update([
                    'transfer_date' => $request->transfer_date,
                    'from_branch_id' => $request->from_branch_id,
                    'to_branch_id' => $request->to_branch_id,
                    'transfer_note' => $request->transfer_note,
                    'transfer_status' => 0,
                    'updated_by' => $request->user()->id,
                ]);
                $data = [];
                foreach ($request->product_id as $key => $item) :
                    $data[] = [
                        'transfer_id' => $id,
                        'product_id' => $item,
                        'qty' => $request->qty[$key],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                endforeach;
                TransferDetails::where('transfer_id', $id)->delete();
                TransferDetails::insert($data);
            });
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage())->withInput($request->all());
        }
        return redirect()->route('frame.transfer')->with("success", "Product transfer updated successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Transfer::findOrFail(decrypt($id))->delete();
        return redirect()->route('frame.transfer')->with("success", "Product transfer deleted successfully!");
    }
}
