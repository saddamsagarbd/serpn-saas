<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index(){
        return view('tenant.sale.index');
    }
    public function pos(){
        return view('tenant.sale.pos');
    }
    public function salesReturn(){
        return view('tenant.sale.sales-return');
    }
    public function customers(){
        return view('tenant.customer.index');
    }

}

