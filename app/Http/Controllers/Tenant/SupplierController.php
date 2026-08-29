<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ChartOfAccount;
use App\Models\Supplier;
use App\Models\SupplierInvoice;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    /**
     * "Payment Terms" dropdown options shown in the form, mapped to the
     * integer payment_terms_days actually stored on the supplier.
     */
    public const PAYMENT_TERMS = [
        0  => 'Cash on Delivery',
        15 => 'Net 15 Days',
        30 => 'Net 30 Days',
        45 => 'Net 45 Days',
        60 => 'Net 60 Days',
    ];

    public const SUPPLIER_TYPES = [
        "fabrics"  => 'Fabrics',
        "trims" => 'Trims & Accessories',
        "yarn" => 'Yarn',
        "packaging" => 'Packaging',
        "general" => 'General / Service'
    ];
    
    public function index(Request $request){
        if ($request->wantsJson() || $request->ajax()) {
            $perPage = $request->query('per_page', 10);
            $search = $request->query('search', '');

            $query = Supplier::with(['account'])->latest();

            // 🔍 গ্লোবাল সার্চ লজিক
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('sku', 'LIKE', "%{$search}%")
                    ->orWhereHas('category', function($catQ) use ($search) {
                        $catQ->where('name', 'LIKE', "%{$search}%");
                    });
                });
            }

            // Laravel-এর বিল্ট-ইন পেজিনেটর যা শুধু ওই পেজের ১০টি ডাটা কুয়েরি করবে
            $suppliers = $query->paginate($perPage);

            return response()->json($suppliers);
        }
        return view('tenant.supplier.index');
    }
    public function create(){
        $categories = Category::where('tenant_id', tenant('id'))->get();
        return view('tenant.supplier.supplier-form', [
            'paymentTerms'  => self::PAYMENT_TERMS,
            'supplierTypes'  => self::SUPPLIER_TYPES,
            'suggestedCode' => Supplier::generateCode(),
        ]);
    }

    public function store($tenant, Request $request) 
    {
        $tenantId = tenant('id');
        // ভ্যালিডেশন
        $data = $request->validate([            
            'supplier_code' => [
                'nullable', 
                'string', 
                'max:50', 
                Rule::unique('suppliers', 'supplier_code')->where('tenant_id', $tenantId)
            ],
            'name'          => ['required', 'string', 'max:150'],
            'supplier_type' => ['required', 'string', 'max:100'],
            'tax_id'        => ['nullable', 'string', 'max:100'],
            'address'       => ['nullable', 'string', 'max:1000'],
            'contact_person'=> ['required', 'string', 'max:150'],
            'email'         => [
                'required', 
                'email', 
                'max:150', 
                Rule::unique('suppliers', 'email')->where('tenant_id', $tenantId)
            ],
            'phone'         => [
                'required', 
                'string', 
                'max:50', 
                Rule::unique('suppliers', 'phone')->where('tenant_id', $tenantId)
            ],
            'payment_terms_days'  => ['nullable', 'integer', Rule::in(array_keys(self::PAYMENT_TERMS))],
            'bank_name'           => ['nullable', 'string', 'max:150'],
            'bank_account_number' => ['nullable', 'string', 'max:100'],
        ]);

        $apAccount = ChartOfAccount::where('is_control_account', true)
            ->where('type', 'liability')
            ->where('code', 'AP')
            ->firstOrFail();

        $data['tenant_id']  = $tenantId;            
        $data['created_by'] = auth()->id();
        $data['coa_id'] = $apAccount->id;

        $supplier = Supplier::create($data);

        return redirect()
            ->route('tenant.purchase.suppliers.index')
            ->with('success', "Supplier \"{$supplier->name}\" ({$supplier->supplier_code}) created.");

    }

    public function edit($tenant, string $id){

        $supplier = Supplier::where('tenant_id', tenant('id'))->findOrFail($id);

        if(!$supplier){
            return redirect()
            ->back()
            ->with('error', "Supplier not found.");
            
        }

        return view('tenant.supplier.supplier-form', [
            'paymentTerms'  => self::PAYMENT_TERMS,
            'supplierTypes'  => self::SUPPLIER_TYPES,            
            'suggestedCode' => Supplier::generateCode(),
            'supplier' => $supplier,
        ]);

    }

    public function update($tenant, Request $request, string $id)
    {
        // 1. Fetch record (throws automatic 404 if not found)
        $supplier = Supplier::where('tenant_id', tenant('id'))->findOrFail($id);

        if(!$supplier){
            return redirect()
            ->back()
            ->with('error', "Supplier not found.");
            
        }

        // 2. Validate input
        $data = $request->validate([
            'name'                 => ['required', 'string', 'max:150'],
            'supplier_type'        => ['required', 'string', 'max:100'],
            'tax_id'               => ['nullable', 'string', 'max:100'],
            'address'              => ['nullable', 'string', 'max:1000'],
            'contact_person'       => ['required', 'string', 'max:150'],
            'email'                => [
                'required', 
                'email', 
                'max:150',
                // Ignore current supplier ID to allow saving without changing email
                Rule::unique('suppliers')->ignore($supplier->id)->where('tenant_id', $tenant)
            ],
            'phone'                => ['required', 'string', 'max:50'],
            'payment_terms_days'   => ['nullable', 'integer', Rule::in(array_keys(self::PAYMENT_TERMS))],
            'bank_name'            => ['nullable', 'string', 'max:150'],
            'bank_account_number'  => ['nullable', 'string', 'max:100'],
            'is_active'            => ['boolean'],
        ]);

        // 3. Track auditor ID
        $data['updated_by'] = auth()->id();

        // 4. Update and redirect
        $supplier->update($data);

        return redirect()
            ->route('tenant.purchase.suppliers.index', $tenant)
            ->with('success', 'Supplier updated successfully.');
    }
}