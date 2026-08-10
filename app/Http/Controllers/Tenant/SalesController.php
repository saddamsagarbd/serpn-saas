<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SalesController extends Controller
{
    public function index(Request $request){
        
        if($request->ajax()){
            $data = SalesOrder::with(['buyer', 'items'])->where('tenant_id', tenant('id'))->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('buyer_po', function($row){
                    return $row->buyer_po_number ? $row->buyer_po_number : 'N/A';
                })
                ->addColumn('buyer_name', function($row){
                    if (!$row->buyer) {
                        return 'N/A';
                    }
                    
                    return html_entity_decode(
                        html_entity_decode($row->buyer->name, ENT_QUOTES, 'UTF-8'), 
                        ENT_QUOTES, 
                        'UTF-8'
                    );
                })
                ->editColumn('po_date', function($row){
                    return $row->po_received_date ?? $row->created_at;
                })
                ->editColumn('delivery_date', function($row){
                    return $row->requested_delivery_date ?? 'N/A';
                })
                ->editColumn('job_mode', function($row){
                    return $row->job_mode ? ($row->distribution_channel ?? '' )."/".$row->job_mode: 'N/A';
                })
                ->editColumn('currency', function($row){
                    return $row->currency ?? 'USD';
                })
                ->editColumn('total_amount', function($row){
                    return $row->total_amount ?? 0.00;
                })
                ->editColumn('status', function($row){
                    return $row->status ?? 'Draft';
                })
                ->rawColumns(['action', 'status', 'currency', 'buyer_name'])
                ->make(true);
            
        }
        return view('tenant.sales-order.index');
        
    }
}