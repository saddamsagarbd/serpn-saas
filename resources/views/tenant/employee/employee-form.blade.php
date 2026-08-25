@extends('layouts.tenant')
@section('title', isset($employee) ? 'Edit Employee' : 'Employee Entry')
@section('content')
<div class="space-y-6" x-data="{ currentTab: 'supplier-form', openModal: false }">
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div x-show="currentTab === 'supplier-form'" x-transition class="space-y-6">
            <div class="border-b border-gray-100 pb-4 mb-6">
                <h3 class="text-lg font-bold text-gray-800">{{ isset($employee) ? 'Edit Employee' : 'Add New Employee' }}</h3>
                <p class="text-xs text-gray-500 mt-1"></p>
            </div>

            <form action="{{ isset($employee) ? route('tenant.employee.update', $employee->id) : route('tenant.employee.store') }}" method="POST" class="space-y-6">
                @csrf

                @if(isset($employee))
                    @method('PUT')
                @endif

                <div>
                    <h4 class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-3">1. Company Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Employee ID (Auto/Manual)</label>
                            <input type="text" name="emp_id" id="emp_id"
                                value="{{ old('emp_id', $employee->emp_id ?? $suggestedCode) }}"
                                placeholder="{COMPANYCODE}10001"
                                class="w-full rounded-lg border border-gray-300 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-600 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="text-xs text-slate-400 mt-1">Leave as-is to auto-generate, or type your own code.</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Employee Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" required
                                value="{{ old('name', $employee->name ?? '') }}"
                                placeholder="e.g. Md. Saddam Hossain"
                                class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-400 @enderror">
                            @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Company</label>
                            <select name="company_id" id="company_id"
                                    class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Department</label>
                            <select name="department_id" id="department_id"
                                    class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Designation</label>
                            <select name="designation_id" id="designation_id"
                                    class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </select>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100">

                <div>
                    <h4 class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-3">2. Contact Person Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Email Address<span class="text-red-500">*</span></label>
                            <input type="email" name="email" id="email" required
                                value="{{ old('email', $employee->email ?? '') }}"
                                placeholder="john@yourdomainname.com"
                                class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-400 @enderror">
                            @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Phone Number<span class="text-red-500">*</span></label>
                            <input type="text" name="phone" id="phone" required
                                value="{{ old('phone', $employee->phone ?? '') }}"
                                placeholder="+880 17XX XXXXXX"
                                class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('phone') border-red-400 @enderror">
                            @error('phone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Emergency Phone Number<span class="text-red-500">*</span></label>
                            <input type="text" name="emergency_phone" id="emergency_phone" required
                                value="{{ old('emergency_phone', $employee->emergency_phone ?? '') }}"
                                placeholder="+880 17XX XXXXXX"
                                class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('phone') border-red-400 @enderror">
                            @error('emergency_phone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100">

        

                <div class="flex justify-end space-x-2 pt-4 border-t border-gray-100 gap-2">
                    <a href="{{ route('tenant.employee.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2 border border-transparent rounded-lg text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm focus:outline-none">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection