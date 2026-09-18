@extends('layouts.tenant')
@section('title', isset($user) ? 'Edit User' : 'User Entry')
@section('content')
<div class="space-y-6" x-data="{ currentTab: 'user-form', openModal: false }">
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div x-show="currentTab === 'user-form'" x-transition class="space-y-6">
            <div class="border-b border-gray-100 pb-4 mb-6">
                <h3 class="text-lg font-bold text-gray-800">{{ isset($user) ? 'Edit User' : 'Add New User' }}</h3>
                <p class="text-xs text-gray-500 mt-1">
                    {{ isset($user) ? 'Modify existing user records and details.' : 'Register a new user into system.' }}
                </p>
            </div>

            <form action="{{ route('tenant.user.store') }}" method="POST" class="space-y-6">
                @csrf

                <input type="hidden" name="user_id" value="{{ $user->id ?? '' }}" />

                <div>
                    <h4 class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-3">1. Contact Person Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                            <input type="text" name="user_name" id="user_name" required
                                value="{{ old('user_name', $user->user_name ?? '') }}"
                                placeholder="John Doe"
                                class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('user_name') border-red-400 @enderror">
                            @error('user_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Email Address<span class="text-red-500">*</span></label>
                            <input type="email" name="email" id="email" required
                                value="{{ old('email', $user->email ?? '') }}"
                                placeholder="john@supplier.com"
                                class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-400 @enderror">
                            @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Phone Number<span class="text-red-500">*</span></label>
                            <input type="text" name="phone" id="phone" required
                                value="{{ old('phone', $user->phone ?? '') }}"
                                placeholder="+880 17XX XXXXXX"
                                class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('phone') border-red-400 @enderror">
                            @error('phone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            @php
                                $roles = [1 => 'Admin', 2 => 'Supervisor', 3 => 'User'];
                            @endphp
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Role</label>
                            <select name="role_id" required class="w-full border border-gray-300 rounded-lg text-xs p-2.5">
                                <option value="">-- Select role --</option>
                                @foreach($roles as $id => $name)
                                    <option value="{{ $id }}" @selected(old('role_id', $user->role_id ?? null) == $id)>
                                        {{ $name }}
                                    </option>
                                @endforeach                             
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-4 border-t border-gray-100 gap-2">
                    <a href="{{ route('tenant.user.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2 border border-transparent rounded-lg text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm focus:outline-none">
                        {{ isset($user) ? 'Update User' : 'Save User' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection