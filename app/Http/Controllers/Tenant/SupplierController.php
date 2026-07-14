<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\Supplier;
use Illuminate\Http\Request;

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
        return view('tenant.supplier.supplier-form', [
            'paymentTerms'  => self::PAYMENT_TERMS,
            'suggestedCode' => Supplier::generateCode(),
        ]);
    }

    public function store(Request $request) 
    {
        // ভ্যালিডেশন
        $data = $request->validate([            
            'supplier_code'        => ['nullable', 'string', 'max:50', 'unique:suppliers,supplier_code'],
            'name'                 => ['required', 'string', 'max:150'],
            'tax_id'               => ['nullable', 'string', 'max:100'],
            'address'              => ['nullable', 'string', 'max:1000'],
            'contact_person'       => ['required', 'string', 'max:150'],
            'email'                => ['required', 'email', 'max:150', 'unique:suppliers,email'],
            'phone'                => ['required', 'string', 'max:50', 'unique:suppliers,phone'],
            'payment_terms_days'   => ['nullable', 'integer', 'in:' . implode(',', array_keys(self::PAYMENT_TERMS))],
            'bank_name'            => ['nullable', 'string', 'max:150'],
            'bank_account_number'  => ['nullable', 'string', 'max:100'],
        ]);

        $apAccount = ChartOfAccount::where('is_control_account', true)
            ->where('type', 'liability')
            ->where('code', 'AP')
            ->firstOrFail();

        $supplier = Supplier::create([
            ...$data,
            'coa_id' => $apAccount->id,
        ]);

        return redirect()
            ->route('tenant.purchase.suppliers')
            ->with('success', "Supplier \"{$supplier->name}\" ({$supplier->supplier_code}) created.");

    }

    public function edit($tenant, string $id){
        dd($id);

    }
    
    public function update($tenant, Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        return redirect()->route('tenant.purchase.suppliers')
            ->with('success', 'Supplier information updated perfectly.');
    }
}
