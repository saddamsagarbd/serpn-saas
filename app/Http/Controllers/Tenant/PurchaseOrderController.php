<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function index(Request $request){

        return view('tenant.purchase.order.index');        
    }

    public function create(){

        return view('tenant.purchase.order.create');        
    }
}