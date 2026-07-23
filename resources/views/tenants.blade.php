@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="tenantsApp()" x-init="init()">
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <!-- TENANTS TAB CONTENT -->
        <div x-transition class="space-y-6">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-800">Tenant / Vendor Accounts</h2>
                <button @click="openModal = true" class="bg-indigo-600 text-white font-bold px-4 py-2.5 rounded-lg hover:bg-indigo-700 shadow-sm transition">
                    + Add New Tenant
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-500 uppercase">Yajra DataTables Server-Side Processing Active</span>
                    <input type="text" placeholder="Search tenants..." class="border border-gray-300 rounded-lg text-xs px-3 py-1.5 focus:outline-none focus:border-indigo-500 w-64">
                </div>
                
                                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-gray-200 text-gray-600 text-xs font-bold uppercase">
                            <th class="p-4">Company</th>
                            <th class="p-4">Subdomain</th>
                            <th class="p-4">Business Type</th>
                            <th class="p-4">Plan</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                        <template x-if="loading">
                            <tr><td colspan="6" class="p-4 text-center text-indigo-600 font-semibold animate-pulse">Loading tenants...</td></tr>
                        </template>
 
                        <template x-if="!loading && tenants.length === 0">
                            <tr><td colspan="6" class="p-4 text-center text-gray-400">No tenants found.</td></tr>
                        </template>
 
                        <template x-if="!loading && tenants.length > 0">
                            <template x-for="tenant in tenants" :key="tenant.id">
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="p-4 font-semibold text-gray-900" x-text="tenant.company_name"></td>
                                    <td class="p-4 font-mono text-indigo-600" x-text="tenant.subdomain"></td>
                                    <td class="p-4">
                                        <span class="px-2 py-1 text-xs font-bold rounded-full bg-slate-100 text-slate-600" x-text="businessTypeLabel(tenant.business_type)"></span>
                                    </td>
                                    <td class="p-4 capitalize" x-text="tenant.plan"></td>
                                    <td class="p-4">
                                        <span :class="tenant.status === 'active' ? 'text-emerald-600' : 'text-rose-600'" class="font-bold text-xs uppercase" x-text="tenant.status"></span>
                                    </td>
                                    <td class="p-4 text-center space-x-1">
                                        <button @click="editTenant(tenant)" class="bg-gray-100 text-gray-600 text-xs px-3 py-1.5 rounded hover:bg-indigo-50 hover:text-indigo-600 font-semibold transition">Edit</button>
                                        <button @click="deleteTenant(tenant)" class="bg-gray-100 text-red-600 text-xs px-3 py-1.5 rounded hover:bg-red-50 font-semibold transition">Delete</button>
                                    </td>
                                </tr>
                            </template>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @include('slices.tenant-modal')
</div>
@endsection
@push('scripts')
<script>
    // All Blade->JS route values isolated in one small, easy-to-audit block.
    // Nothing here mixes multi-line JS objects with nested Blade quoting —
    // that combination is what silently broke the previous inline x-data.
    window.tenantRoutes = {
        index         : '{{ route('tenants') }}',
        store         : '{{ route('tenants.store') }}',
        updateBase    : '{{ route('tenants.update', ':id') }}',
        destroyBase   : '{{ route('tenants.destroy', ':id') }}',
        plans         : '{{ route('plans') }}',
    };
</script>
<script>
    function tenantsApp() {
        return {
            openModal: false,
            isEdit: false,
            formAction: window.tenantRoutes.store,
            formErrors: [],    
            plans: [],
            tenants: [],
            loading: false,
            searchQuery: '',
    
            // Tenant provisioning model.
            // business_type is the critical field — it drives which vertical
            // modules (menu.php business_types tags) this tenant sees.
            tenantData: {
                id: '',
                company_name: '',
                subdomain: '',
                business_type: '',   // garment | real_estate | manufacturing | general_retail
                owner_name: '',
                owner_email: '',
                owner_phone: '',
                plan: '',
                trial_months: '1',
                status: 'active',    // active | suspended
            },
    
            businessTypes: [
                { value: 'garment',        label: 'Garment / Buying House' },
                { value: 'real_estate',    label: 'Real Estate' },
                { value: 'manufacturing',  label: 'General Manufacturing' },
                { value: 'general_retail', label: 'General Retail / Trading' },
            ],
    
            init() {
                this.fetchTenants();
                this.fetchPlans();
            },
    
            initCreate() {
                this.isEdit = false;
                this.formErrors = [];
                this.formAction = window.tenantRoutes.store;
                this.tenantData = {
                    id: '', company_name: '', subdomain: '', business_type: '',
                    owner_name: '', owner_email: '', owner_phone: '',
                    plan: '', trial_months: '1', status: 'active',
                };
                this.openModal = true;
            },
    
            editTenant(tenant) {
                this.isEdit = true;
                this.formErrors = [];
                this.formAction = window.tenantRoutes.updateBase.replace(':id', tenant.id);
    
                this.tenantData = {
                    id: tenant.id,
                    company_name: tenant.company_name,
                    subdomain: tenant.subdomain,
                    business_type: tenant.business_type,
                    owner_name: tenant.owner_name,
                    owner_email: tenant.owner_email,
                    owner_phone: tenant.owner_phone,
                    plan: tenant.plan,
                    trial_months: tenant.trial_months ?? '1',
                    status: tenant.status,
                };
                this.openModal = true;
            },
    
            deleteTenant(tenant) {
                if (!confirm('Suspend tenant "' + tenant.company_name + '"? Their data stays intact but access is blocked.')) return;
    
                const token = document.querySelector('meta[name="csrf-token"]')?.content;
                const url = window.tenantRoutes.destroyBase.replace(':id', tenant.id);
    
                fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Suspend failed');
                    return response.json();
                })
                .then(data => {
                    this.fetchTenants();
                    if (typeof toastr !== 'undefined') toastr.success(data.message || 'Tenant suspended');
                })
                .catch(() => alert('Failed to suspend tenant.'));
            },
    
            fetchTenants() {
                this.loading = true;
                let url = window.tenantRoutes.index;
                if (this.searchQuery) {
                    url += '?search[value]=' + encodeURIComponent(this.searchQuery);
                }
    
                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                .then(response => response.json())
                .then(res => {
                    this.tenants = res.data || [];
                    this.loading = false;
                })
                .catch(err => {
                    console.error('Error:', err);
                    this.loading = false;
                });
            },
    
            fetchPlans() {
                fetch(window.tenantRoutes.plans, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                .then(response => response.json())
                .then(res => {
                    this.plans = res.data || [];
                })
                .catch(err => console.error('Error fetching plans:', err));
            },
    
            saveTenant() {
                this.formErrors = [];
    
                if (!this.tenantData.business_type) {
                    this.formErrors = ['Please select a Business Type — this determines which modules the tenant sees.'];
                    return;
                }
    
                const token = document.querySelector('meta[name="csrf-token"]')?.content;
                const formData = { ...this.tenantData };
    
                fetch(this.formAction, {
                    method: this.isEdit ? 'PUT' : 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify(formData)
                })
                .then(async response => {
                    const data = await response.json();
                    if (!response.ok) {
                        if (data.errors) {
                            this.formErrors = Object.values(data.errors).flat();
                        } else {
                            this.formErrors = [data.message || 'Something went wrong.'];
                        }
                        throw new Error('Validation Error');
                    }
                    return data;
                })
                .then(data => {
                    this.openModal = false;
                    this.fetchTenants();
                    if (typeof toastr !== 'undefined') toastr.success(data.message || 'Tenant saved');
                })
                .catch(() => {});
            },
    
            businessTypeLabel(value) {
                const found = this.businessTypes.find(b => b.value === value);
                return found ? found.label : value;
            }
        };
    }
</script>
@endpush
