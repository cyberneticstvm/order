<?php

namespace App\Http\Controllers;

use App\Models\Camp;
use App\Models\CampType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CampController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    function __construct(){
        $this->middleware('permission:camp-list|camp-create|camp-edit|camp-delete', ['only' => ['index','store']]);
        $this->middleware('permission:camp-create', ['only' => ['create','store']]);
        $this->middleware('permission:camp-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:camp-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $camps = Camp::withTrashed()->latest()->get();
        return view('backend.camp.index', compact('camps'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::pluck('name', 'id');
        $ctypes = CampType::pluck('name', 'id');
        return view('backend.camp.create', compact('users', 'ctypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'from_date' => 'required',
            'to_date' => 'required',
            'venue' => 'required',
            'address' => 'required',
            'cordinator' => 'required',
            'camp_type' => 'required',
        ]);

        $input = $request->all();
        $input['created_by'] = $request->user()->id;
        $input['updated_by'] = $request->user()->id;
        $input['branch_id'] = branch()->id;
        $input['camp_id'] = camptId()->cid;
        Camp::create($input);
        return redirect()->route('camps')->with('success', 'Camp has been created successfully!');
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
        $camp = Camp::findOrFail(decrypt($id));
        if (! Gate::allows('edit-delete-camp', [$branch = branch(), $camp])) {
            return redirect()->back()->with('error', 'Camp created for another branch could not be updated!');
        }
        $users = User::pluck('name', 'id');
        $ctypes = CampType::pluck('name', 'id');
        return view('backend.camp.edit', compact('users', 'ctypes', 'camp'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'from_date' => 'required',
            'to_date' => 'required',
            'venue' => 'required',
            'address' => 'required',
            'cordinator' => 'required',
            'camp_type' => 'required',
        ]);

        $input = $request->all();
        $input['updated_by'] = $request->user()->id;
        $camp = Camp::findOrFail($id);
        $camp->update($input);
        return redirect()->route('camps')->with('success', 'Camp has been updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $camp = Camp::findOrFail(decrypt($id));
        if (! Gate::allows('edit-delete-camp', [$branch = branch(), $camp])) {
            return redirect()->back()->with('error', 'Camp created for another branch could not be deleted!');
        }
        $camp->delete();
        return redirect()->route('camps')->with('success', 'Camp has been deleted successfully!');
    }
}
