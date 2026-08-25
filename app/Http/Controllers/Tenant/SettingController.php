<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SettingController extends Controller
{
    public function index(){

    }

    public function company(Request $request){
        if($request->ajax()){
            $companies = Company::where('tenant_id', tenant('id'))->get();

            return DataTables::of($companies)
                ->addIndexColumn()
                ->addColumn('code', function($row){
                    return $row->company_code ?? 'N/A';
                })
                ->addColumn('name', function($row){
                    return $row->name ?? 'N/A';
                })
                ->editColumn('address', function($row){
                    return $row->address ?? 'N/A';
                })
                ->rawColumns(['code'])
                ->make(true);

        }
        return view('tenant.settings.company');
    }
    public function companyStore($tenant, Request $request){

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $data = [
                "tenant_id"         => tenant('id'),
                "name"              => trim($request->name),
                "company_code"      => trim($request->code),
                "address"           => trim($request->address),
            ];

            Company::create($data);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Company created successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Something went wrong.'.$e->getMessage()]);
        }

    }

    public function companyUpdate(Request $request, $tenant, String $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string',
        ]);

        DB::beginTransaction();

        try {

            $company = Company::findOrFail($id);

            $company->update([
                "name"              => trim($request->name),
                "company_code"      => trim($request->code),
                "address"           => trim($request->address),
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Company updated successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Something went wrong.'.$e->getMessage()]);
        }
    }

    public function department(Request $request){
        if($request->all()){

        }
        return view('tenant.settings.department');
    }

    public function designation(Request $request){
        if($request->all()){

        }
        return view('tenant.settings.designation');
    }
}
