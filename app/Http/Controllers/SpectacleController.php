<?php

namespace App\Http\Controllers;

use App\Models\Power;
use App\Models\Registration;
use App\Models\Spectacle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SpectacleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $optometrists, $doctors, $powers;

    function __construct()
    {
        $this->middleware('permission:spectacle-list|spectacle-create|spectacle-edit|spectacle-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:spectacle-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:spectacle-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:spectacle-delete', ['only' => ['destroy']]);

        $this->middleware(function ($request, $next) {

            $this->optometrists = User::role('Optometrist')->pluck('name', 'id');
            $this->doctors = User::role('Doctor')->pluck('name', 'id');
            $this->powers = Power::all();

            return $next($request);
        });
    }

    public function index()
    {
        $spectacles = Spectacle::withTrashed()->latest()->get();
        return view('backend.spectacle.index', compact('spectacles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $id, string $type)
    {
        $registration = Registration::findOrFail(decrypt($id));
        $optometrists = $this->optometrists;
        $doctors = $this->doctors;
        $powers = $this->powers;
        return view('backend.spectacle.create', compact('registration', 'optometrists', 'doctors', 'powers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'customer_id' => 'required',
            'registration_id' => 'required',
        ]);
        $input = $request->all();
        $input['created_by'] = $request->user()->id;
        $input['updated_by'] = $request->user()->id;
        $input['branch_id'] = Session::get('branch');
        Spectacle::create($input);
        return redirect()->route('spectacles')->with("success", "Spectacle prescription recorded successfully");
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
        $spectacle = Spectacle::findOrFail(decrypt($id));
        $optometrists = $this->optometrists;
        $doctors = $this->doctors;
        $powers = $this->powers;
        return view('backend.spectacle.edit', compact('spectacle', 'optometrists', 'doctors', 'powers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $input = $request->all();
        $input['updated_by'] = $request->user()->id;
        Spectacle::findOrFail($id)->update($input);
        return redirect()->route('spectacles')->with("success", "Spectacle prescription updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Spectacle::findOrFail(decrypt($id))->delete();
        return redirect()->route('spectacles')->with("success", "Spectacle prescription deleted successfully");
    }
}
