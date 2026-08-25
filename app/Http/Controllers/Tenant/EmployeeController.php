<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request){
        if ($request->ajax()) {

        }
        return view('tenant.employee.index');
    }
    public function create(){
        return view('tenant.employee.employee-form', [
            'suggestedCode' => '',
        ]);
    }
}
