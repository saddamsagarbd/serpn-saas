<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Style;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public const SUPPLIER_TYPES = [
        "fabrics"  => 'Fabrics',
        "trims" => 'Trims & Accessories',
        "yarn" => 'Yarn',
        "packaging" => 'Packaging',
        "general" => 'General / Service'
    ];
    
    public function index(Request $request){

        return view('tenant.purchase.order.index');        
    }

    public function create(){
        $suppliers = Supplier::where('tenant_id', tenant('id'))->where('is_active', 1)->get();
        $styles = Style::where('tenant_id', tenant('id'))->get();
        $supplier_types = self::SUPPLIER_TYPES;
        return view('tenant.purchase.order.create', compact('suppliers', 'supplier_types', 'styles'));        
    }

    public function store(Request $request){
        dd($request->all());
    }
}