<?php

namespace App\Http\Controllers;

use App\Models\Head;
use App\Models\IncomeExpense;
use App\Models\PaymentMode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class IncomeExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $heads, $iandes;

    function __construct()
    {
        $this->middleware('permission:income-expense-list|income-expense-create|income-expense-edit|income-expense-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:income-expense-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:income-expense-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:income-expense-delete', ['only' => ['destroy']]);

        $this->middleware(function ($request, $next) {
            $this->iandes = IncomeExpense::when(Auth::user()->roles->first()->id != 1, function ($q) {
                return $q->where('branch_id', Session::get('branch'));
            })->whereDate('created_at', Carbon::today())->withTrashed()->latest()->get();
            return $next($request);
        });

        //$this->heads = Head::selectRaw("id, CONCAT_WS('-', name, category) AS name")->orderBy('name')->pluck('name', 'id');
    }

    public function index()
    {
        $iandes = $this->iandes;
        return view('backend.iande.index', compact('iandes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($category)
    {
        $heads = Head::selectRaw("id, CONCAT_WS('-', name, category) AS name")->where('category', $category)->orderBy('name')->pluck('name', 'id');
        $pmodes = PaymentMode::orderBy('name')->get();
        return view('backend.iande.create', compact('heads', 'pmodes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'head_id' => 'required',
            'description' => 'required',
            'amount' => 'required',
            'payment_mode' => 'required',
        ]);
        $input = $request->all();
        $input['date'] = Carbon::today();
        $input['branch_id'] = branch()->id;
        $input['created_by'] = $request->user()->id;
        $input['updated_by'] = $request->user()->id;
        $head = Head::findOrFail($request->head_id);
        $input['category'] = $head->category;
        if ($head->category == 'expense' && $head->daily_expense_limit == 1) :
            if (!isExpenseLimitReached($request->amount)) :
                IncomeExpense::create($input);
            else :
                return redirect()->back()->with("error", "Daily expense limit reached for this branch!")->withInput($request->all());
            endif;
        else :
            IncomeExpense::create($input);
        endif;
        return redirect()->route('iande')
            ->with('success', 'Income/Expense has been created successfully');
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
        $iande = IncomeExpense::findOrFail(decrypt($id));
        $heads = Head::selectRaw("id, CONCAT_WS('-', name, category) AS name")->where('category', $iande->category)->orderBy('name')->pluck('name', 'id');
        $pmodes = PaymentMode::orderBy('name')->get();
        return view('backend.iande.edit', compact('iande', 'heads', 'pmodes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'head_id' => 'required',
            'description' => 'required',
            'amount' => 'required',
            'payment_mode' => 'required',
        ]);
        $input = $request->all();
        $ie = IncomeExpense::findOrFail($id);
        $head = Head::findOrFail($request->head_id);
        $input['updated_by'] = $request->user()->id;
        $input['category'] = $head->category;
        if ($head->category == 'expense' && $head->daily_expense_limit == 1) :
            if (!isExpenseLimitReached($request->amount, $ie->getOriginal('amount'))) :
                $ie->update($input);
            else :
                return redirect()->back()->with("error", "Daily expense limit reached for this branch!")->withInput($request->all());
            endif;
        else :
            $ie->update($input);
        endif;
        return redirect()->route('iande')
            ->with('success', 'Income/Expense has been updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        IncomeExpense::findOrFail(decrypt($id))->delete();
        return redirect()->route('iande')
            ->with('success', 'Income/Expense has been deleted successfully');
    }
}
