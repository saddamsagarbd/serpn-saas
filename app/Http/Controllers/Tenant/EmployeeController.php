<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    public function index($tenant, Request $request){
        if ($request->ajax()) {
            $perPage = $request->get('per_page', 10);
            $search = $request->get('search');

            $employees = Employee::with(['company:id,name'])
                ->when($search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('employee_id', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhereHas('company', function ($dq) use ($search) {
                                $dq->where('name', 'like', "%{$search}%");
                        });
                    });
                })
                ->latest()
                ->paginate($perPage);

            // Alpine.js এর চাহিদানুযায়ী ম্যাপ করা ফরম্যাট
            return response()->json([
                'data' => $employees->map(function ($emp) {
                    $status = 'active';
                    // 0=In-active; 1=active; 2=suspend; 3=retired;
                    if($emp->status == 0){
                        $status = 'in-active';
                    }else if($emp->status == 2){
                        $status = 'Suspended';
                    }else if($emp->status == 3){
                        $status = 'Retired';
                    }
                    return [
                        'id' => $emp->id,
                        'emp_id' => $emp->employee_id,
                        'name' => $emp->name,
                        'department' => $emp->department?->name ?? 'N/A',
                        'email' => $emp->email,
                        'phone' => $emp->phone,
                        'status' => ucfirst($status ?? 'active'),
                    ];
                }),
                'current_page' => $employees->currentPage(),
                'last_page' => $employees->lastPage(),
                'total' => $employees->total(),
                'per_page' => $employees->perPage(),
            ]);
        }
        return view('tenant.hrm.employee.index');
    }
    public function create(){
        $companies = Company::get();
        return view('tenant.hrm.employee.employee-form', [
            'suggestedCode' => '',
            'companies' => $companies
        ]);
    }

    public function store($tenant, StoreEmployeeRequest $request){

        DB::beginTransaction();

        try {
            $validated = $request->validated();

            $validated["tenant_id"] = tenant('id');

            $employee = Employee::create($validated);

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Employee created successfully!',
                    'data' => $employee
                ], 201);
            }

            return redirect()
                ->route('tenant.hrm.employee.index')
                ->with('success', 'Employee created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Employee Store Error: ' . $e->getMessage(), [
                'tenant' => $tenant,
                'input' => $request->except(['password', '_token'])
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create employee: ' . $e->getMessage());
        }
    }
}
