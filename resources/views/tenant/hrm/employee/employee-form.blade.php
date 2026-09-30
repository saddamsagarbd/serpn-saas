@extends('layouts.tenant')
@section('title', isset($employee) ? 'Edit Employee' : 'Employee Entry')
@section('content')
<div class="space-y-6" x-data="employeeApp({{ json_encode($employee ?? null) }}, {{ json_encode($companies ?? []) }}, {{ json_encode($departments ?? []) }}, {{ json_encode($designations ?? []) }})">
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div x-transition class="space-y-6">
            <div class="border-b border-gray-100 pb-4 mb-6">
                <h3 class="text-lg font-bold text-gray-800">{{ isset($employee) ? 'Edit Employee' : 'Add New Employee' }}</h3>
                <p class="text-xs text-gray-500 mt-1">Fill in the employee details below.</p>
            </div>

            <form action="{{ isset($employee) ? route('tenant.hrm.employee.update', $employee->id) : route('tenant.hrm.employee.store') }}" method="POST" class="p-6 space-y-6">
                @csrf
                @if(isset($employee))
                    @method('PUT')
                @endif

                <div>
                    <h4 class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-3">1. Company Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Employee ID</label>
                            <input type="text" name="employee_id" x-model="form.employee_id" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500" placeholder="e.g. EMP-1001">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Employee Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" x-model="form.name" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500" placeholder="Enter full name" required>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Company <span class="text-red-500">*</span></label>
                            <select name="company_id" x-model="form.company_id" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500" required>
                                <option value="">-- Choose Company --</option>
                                <template x-for="company in companies" :key="company.id">
                                    <option :value="company.id" :selected="String(company.id) === String(form.company_id)" x-text="company.name + (company.company_code ? ' - ' + company.company_code : '')"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Department <span class="text-red-500">*</span></label>
                            <select name="department_id" x-model="form.department_id" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500">
                                <option value="">-- Choose Department --</option>
                                <template x-for="dept in departments" :key="dept.id">
                                    <option :value="dept.id" :selected="String(dept.id) === String(form.department_id)" x-text="dept.name"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Designation <span class="text-gray-400">(Optional)</span></label>
                            <select name="designation_id" x-model="form.designation_id" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500">
                                <option value="">-- Choose Designation (Optional) --</option>
                                <template x-for="design in designations" :key="design.id">
                                    <option :value="design.id" :selected="String(design.id) === String(form.designation_id)" x-text="design.name"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Sensor ID</label>
                            <input type="text" name="sensor_id" x-model="form.sensor_id" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500" placeholder="e.g. 10060775">
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100">

                <div>
                    <h4 class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-3">2. Contact Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                            <input type="email" name="email" x-model="form.email" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500" placeholder="employee@company.com" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
                            <input type="text" name="phone" x-model="form.phone" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500" placeholder="e.g. +8801700000000" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Emergency Phone Number</label>
                            <input type="text" name="emergency_contact" x-model="form.emergency_contact" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500" placeholder="Emergency contact number">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-4 border-t border-gray-100 gap-2">
                    <a href="{{ route('tenant.hrm.employee.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2 border border-transparent rounded-lg text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm focus:outline-none">
                        Save Employee
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function employeeApp(employeeData, companies, departments, designations) {
    return {
        companies: companies || [],
        departments: departments || [],
        designations: designations || [],
        form: {
            employee_id       : employeeData?.employee_id || '',
            sensor_id         : employeeData?.sensor_id || '',
            name              : employeeData?.name || '',
            company_id        : employeeData?.company_id || '',
            department_id     : employeeData?.department_id || '',
            designation_id    : employeeData?.designation_id || '',
            email             : employeeData?.email || '',
            phone             : employeeData?.phone || '',
            emergency_contact : employeeData?.emergency_contact || ''
        }
    }
}
</script>
@endpush