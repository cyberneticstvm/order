<?php

namespace App\Http\Controllers;

use App\Models\ProductDamage;
use App\Models\PurchaseReturn;
use Illuminate\Http\Request;

class PurchaseReturnController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:purchase-return-list|purchase-return-create|purchase-return-edit|purchase-return-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:purchase-return-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:purchase-return-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:purchase-return-delete', ['only' => ['destroy']]);
    }

    function index()
    {
        $returns = PurchaseReturn::all();
        return view('backend.purchase.return.index', compact('returns'));
    }

    function store(Request $request)
    {
        return response()->json([
            'success' => 'hi',
        ]);
    }
}
