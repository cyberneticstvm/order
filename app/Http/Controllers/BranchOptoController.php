<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchOpto;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class BranchOptoController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    function __construct()
    {
        $this->middleware('permission:branch-opto-list|branch-opto-create|branch-opto-edit|branch-opto-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:branch-opto-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:branch-opto-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:branch-opto-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $bos = BranchOpto::withTrashed()->get();
        return view('backend.branch-opto.index', compact('bos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $branches = Branch::whereIn('type', ['branch'])->get();
        $users = User::whereHas('roles', function ($q) {
            return $q->whereIn('name', ['Optometrist', 'Doctor']);
        })->get();
        return view('backend.branch-opto.create', compact('users', 'branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'branch_id' => 'required',
            'user_id' => 'required',
        ]);
        try {
            $input = $request->all();
            $input['designation'] = User::findOrFail($request->user_id)->roles->first()->name;
            BranchOpto::create($input);
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage())->withInput($request->all());
        }
        return redirect()->route('bo')->with("success", "Record created successfully");
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
        $bo = BranchOpto::findOrFail(decrypt($id));
        $branches = Branch::whereIn('type', ['branch'])->get();
        $users = User::whereHas('roles', function ($q) {
            return $q->whereIn('name', ['Optometrist', 'Doctor']);
        })->get();
        return view('backend.branch-opto.edit', compact('users', 'branches', 'bo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'branch_id' => 'required',
            'user_id' => 'required',
        ]);
        try {
            $input = $request->all();
            $input['designation'] = User::findOrFail($request->user_id)->roles->first()->name;
            BranchOpto::findOrFail($id)->update($input);
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage())->withInput($request->all());
        }
        return redirect()->route('bo')->with("success", "Record updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $bo = BranchOpto::withTrashed()->findOrFail(decrypt($id));
        if ($bo->deleted_at) :
            $bo->restore();
        else :
            $bo->delete();
        endif;
        return redirect()->route('bo')->with("success", "Record updated successfully");
    }
}
