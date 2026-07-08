<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index() {
        return view('tenant.purchase.purchase-order');
    }
    public function purchaseForm() {
        return view('tenant.purchase.po-form');
    }
    public function poCreate(Request $request) {
        return response()->json([]);
    }
    public function goodsReceivedNotes(){
        return view('tenant.purchase.goods-received-notes');
    }
    public function saveGRNTransaction(Request $request){

        // track transaction

        // after success hit stock balance table

    }
    public function suppliers(){
        return view('tenant.supplier.index');
    }
    public function suppliersForm(){
        return view('tenant.supplier.supplier-form');
    }
}
