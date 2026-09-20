<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Permission;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\SendUserCredentials;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Event\RequestEvent;
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

    public function user(Request $request){
        if($request->ajax()){
            $users = User::where('tenant_id', tenant('id'))->get();

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('name', function($row){
                    return $row->name ?? 'N/A';
                })
                ->editColumn('email', function($row){
                    return $row->email ?? 'N/A';
                })
                ->editColumn('phone', function($row){
                    return $row->phone ?? 'N/A';
                })
                ->editColumn('role', function($row){
                    if($row->role_id == 1){
                        $title = "Admin";
                        $badge = "bg-danger";                        
                    }else if($row->role_id == 2){
                        $title = "Supervisor";
                        $badge = "bg-primary";                        
                    }else {
                        $title = "User";
                        $badge = "bg-success";                        
                    }
                    return '<span class="badge '. $badge .'">'. $title .'</span>';
                })
                ->rawColumns(['role'])
                ->make(true);

        }
        return view('tenant.settings.user.index');
    }
    
    public function userCreate(){
        return view('tenant.settings.user.create');
    }
    
    public function userSave(Request $request){

        $userId = $request->user_id ?? null;

        $request->validate([
            'user_name' => ['required', 'string', 'max:255'],
            'email'     => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'phone'     => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'phone')->ignore($userId),
            ],
        ]);

        $now = Carbon::now();
        $currentUser = auth()->id() ?? 'system';

        DB::beginTransaction();

        try {

            $defaultPassword = $request->phone ?? 'erp247@2026';

            $user = $userId ? User::findOrFail($userId) : new User();

            $user->name    = $request->user_name;
            $user->email   = $request->email;
            $user->phone   = $request->phone;
            $user->role_id = $request->role_id ?? 3;

            if ($userId) {
                $user->updated_at = $now;
                $user->updated_by = $currentUser;
                $message = "User information updated successfully";
            } else {
                $user->tenant_id  = tenant('id');
                $user->password   = Hash::make($defaultPassword);
                $user->created_at = $now;
                $user->created_by = $currentUser;
                $message = "User registered successfully";
            }

            // Eloquent automatically updates created_at and updated_at
            $user->save();

            if (!$userId) {
                // Resolve the current tenant instance in multi-tenant setup
                $tenant = tenant(); 

                if ($tenant) {
                    $centralDomain = config('tenancy.central_domains')[0] ?? $request->getHost();
                    $loginUrl = 'http://' . $tenant->id . '.' . $centralDomain . '/login';

                    $recipientEmail = $user->email ?? NULL;

                    // $recipientEmail = 'saddamsagar02@gmail.com';

                    if(!empty($recipientEmail)) {
                        Log::info('before');
                        Notification::route('mail', $recipientEmail)->notify(new SendUserCredentials($user, $defaultPassword, $loginUrl));
                        Log::info('after');
                    }
                }
            }

            DB::commit();

            return redirect()->route('tenant.user.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::info('Something went wrong.'.$e->getMessage());
            return redirect()->route('tenant.user.create')->with('error', 'Something went wrong.'.$e->getMessage());
        }
    }

    public function userPermission($tenant, String $id){
        $employee = User::findOrFail($id);

        $userPermissions = Permission::where('user_id', $employee->id)
        ->get()
        ->keyBy('module');
        
        $tenantData = Tenant::with(['plan'])->where('id', tenant('id'))->first();

        $planFeatures = $tenantData->plan->features ? array_keys($tenantData->plan->features) : [];
        
        return view('tenant.settings.user.permission', compact('tenantData', 'employee', 'userPermissions', 'planFeatures'));        
    }

    public function userPermissionSave(Request $request) {
        $employee = User::findOrFail($request->user_id);

        if(!$employee){
            return redirect()->back()->with('error', 'No user found!');
        }

        $permissionsData = $request->input('permissions', []);

        foreach ($permissionsData as $routeKey => $actions) {
            Permission::updateOrCreate(
                [
                    'user_id' => $employee->id,
                    'module'  => $routeKey, // e.g. 'tenant.inventory.categories.index'
                ],
                [
                    'create'  => isset($actions['create']) ? 1 : 0,
                    'read'    => isset($actions['read']) ? 1 : 0,
                    'update'  => isset($actions['update']) ? 1 : 0,
                    'delete'  => isset($actions['delete']) ? 1 : 0,
                    'cancle'  => isset($actions['cancel']) ? 1 : 0,  // DB Column name is 'cancle'
                    'approve' => isset($actions['approval']) ? 1 : 0, // DB Column name is 'approve'
                ]
            );
        }

        return redirect()->back()->with('success', 'Permissions updated successfully!');
    }
}