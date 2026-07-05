@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="{ currentTab: 'tenants', openModal: false, openModal: {{ $errors->any() ? 'true' : 'false' }} }">
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <!-- TENANTS TAB CONTENT -->
        <div x-show="currentTab === 'tenants'" x-transition class="space-y-6">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-slate-800 tracking-tight">Subscription & Price Packaging Matrix</h2>
                    <p class="text-xs text-gray-400 mt-0.5">ম্যানেজমেন্ট প্যানেল থেকে সরাসরি SaaS প্যাকেজ এবং প্রাইসিং নিয়ন্ত্রণ করুন</p>
                </div>
                <button @click="openModal = true" class="bg-blue-600 text-white font-bold text-xs px-4 py-2.5 rounded-lg hover:bg-blue-700 shadow-sm transition">
                    + Create New Plan
                </button>
            </div>

            @if(session('plan_success'))
                <div class="bg-green-500 text-white p-3 rounded-lg text-xs font-bold shadow">
                    {{ session('plan_success') }}
                </div>
            @endif
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                @forelse($plans as $plan)
                    <div class="bg-white border {{ $plan->code === 'premium' || $plan->code === 'enterprise' ? 'border-2 border-blue-600 relative' : 'border-gray-200' }} p-6 rounded-2xl shadow-sm space-y-4">
                        @if($plan->code === 'premium' || $plan->code === 'enterprise')
                            <div class="absolute top-0 right-6 -translate-y-1/2 bg-blue-600 text-white text-[9px] font-bold px-3 py-0.5 rounded-full uppercase tracking-wider">Top Tier</div>
                        @endif
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-slate-800 text-base">{{ $plan->title }}</h3>
                            <span class="{{ $plan->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} text-[10px] font-bold px-2 py-0.5 rounded">
                                {{ $plan->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <p class="text-2xl font-black text-slate-800">৳ {{ number_format($plan->price, 0) }} <span class="text-xs font-normal text-gray-400">/ {{ $plan->billing_interval }} {{ $plan->billing_period }}</span></p>
                        
                        <div class="border-t border-gray-100 pt-3 space-y-2 text-xs text-slate-600 font-medium">
                            <p>📦 Product Limit: <span class="font-bold text-slate-800">{{ $plan->max_product_limit == -1 ? 'Unlimited' : $plan->max_product_limit . ' Items' }}</span></p>
                            <p>📄 Invoice Limit: <span class="font-bold text-slate-800">{{ $plan->max_invoice_limit == -1 ? 'Unlimited' : $plan->max_invoice_limit . ' / Month' }}</span></p>

                            @if(isset($plan->features))
                                @php 
                                    $default_features = config('saas.default_features', []);
                                    $selectable_features = config('saas.selectable_features', []);
                                    $plan_features = $plan->features ?? [];
                                @endphp
                                
                                @foreach($default_features as $dfeature)
                                    <p class="text-slate-600 flex items-center gap-1.5">
                                        <span class="text-emerald-500 font-bold">✔️</span> {{ $dfeature }}
                                    </p>
                                @endforeach

                                @foreach($plan_features as $key => $value)
                                    {{-- ভ্যালু ১ (অন) থাকলে এবং কনফিগ ফাইলে সেই ফিচারের নাম ডিফাইন করা থাকলেই কেবল দেখাবে --}}
                                    @if($value == 1 && isset($selectable_features[$key]))
                                        <p class="text-blue-600 font-semibold flex items-center gap-1.5">
                                            <span class="text-emerald-500 font-bold">✔️</span> {{ $selectable_features[$key] }}
                                        </p>
                                    @endif
                                @endforeach
                            @endif
                            
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-6 rounded-2xl border border-gray-200 text-center text-gray-400 text-xs font-semibold col-span-12">
                        কোনো প্যাকেজ প্ল্যান তৈরি করা হয়নি। ডানপাশের বাটন থেকে প্রথম প্ল্যানটি তৈরি করুন।
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    @include('slices.plan-modal')
</div>
@endsection