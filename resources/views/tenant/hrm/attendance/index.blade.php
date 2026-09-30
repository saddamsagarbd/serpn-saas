@extends('layouts.tenant')
@section('title', 'Attendance Master')

@section('content')
<div class="space-y-6" x-data="{
        attendances: [],
        companies: [],
        departments: [],
        filters: {
            from_date: '{{ date('Y-m-d') }}',
            to_date: '{{ date('Y-m-d') }}',
            company_id: '',
            department_id: '',
            search: '',
            status: ''
        },
        page: 1,
        perPage: 15,
        lastPage: 1,
        total: 0,
        loading: false,
        syncing: false,
        hasSearched: false,

        async init() {
            await this.loadFilterData();
        },

        async loadFilterData() {
            try {
                let res = await fetch(`{{ route('tenant.hrm.attendance.filters', tenant()) }}`);
                let data = await res.json();
                this.companies = data.companies;
                this.departments = data.departments;
            } catch (e) {
                console.error('Filter data load error:', e);
            }
        },

        async fetchAttendances() {
            if (!this.filters.from_date || !this.filters.to_date) {
                alert('Please select both From Date and To Date');
                return;
            }

            this.loading = true;
            this.hasSearched = true;

            try {
                let query = new URLSearchParams({
                    page: this.page,
                    per_page: this.perPage,
                    from_date: this.filters.from_date,
                    to_date: this.filters.to_date,
                    company_id: this.filters.company_id,
                    department_id: this.filters.department_id,
                    search: this.filters.search,
                    status: this.filters.status
                }).toString();

                let response = await fetch(`{{ route('tenant.hrm.attendance.index', tenant()) }}?${query}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                let data = await response.json();

                this.attendances = data.data;
                this.lastPage = data.last_page || 1;
                this.total = data.total || 0;
            } catch (error) {
                console.error('Error fetching attendance:', error);
            } finally {
                this.loading = false;
            }
        },

        async syncDeviceData() {
            const result = await Swal.fire({
                title               : 'Alert',
                text                : 'Are you sure you want to sync punch logs?',
                icon                : 'warning',
                showCancelButton    : true,
                confirmButtonColor  : '#10B981',
                confirmButtonText   : 'Sync',
                cancelButtonText    : 'Cancel'
            });

            if (result.isConfirmed) {
                this.syncing = true;
                try {
                    let res = await fetch(`{{ route('tenant.hrm.attendance.sync', tenant()) }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ 
                            from_date: this.filters.from_date,
                            to_date: this.filters.to_date 
                        })
                    });
                    let result = await res.json();

                    if(result.success){
                        Swal.fire({
                            title: 'Synced!',
                            text: `${result.message}.`,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        if (this.hasSearched) {
                            this.fetchAttendances();
                        }
                    } else {                        
                        Swal.fire({
                            title: 'Sync Failed!',
                            text: `${result.message}.`,
                            icon: 'error',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                } catch (e) {
                    Swal.fire({
                        title: 'Sync Failed!',
                        text: `Device sync failed!`,
                        icon: 'error',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } finally {
                    this.syncing = false;
                }
            }
        },

        nextPage() { if (this.page < this.lastPage) { this.page++; this.fetchAttendances(); } },
        prevPage() { if (this.page > 1) { this.page--; this.fetchAttendances(); } },
        resetFilters() {
            this.filters.company_id = '';
            this.filters.department_id = '';
            this.filters.search = '';
            this.filters.status = '';
            this.filters.from_date = '{{ date('Y-m-d') }}';
            this.filters.to_date = '{{ date('Y-m-d') }}';
            this.page = 1;
            this.attendances = [];
            this.hasSearched = false;
        }
    }">

    <!-- Single Row Header & Filter Panel -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <div class="flex flex-wrap xl:flex-nowrap items-center justify-between gap-4">
            
            <!-- Left: Title Section -->
            <div class="min-w-max">
                <h2 class="text-lg font-bold text-gray-800 leading-tight">Daily Attendance</h2>
                <p class="text-xs text-gray-500">Monitor employee punch logs and daily records</p>
            </div>

            <!-- Right: Single Row Controls (Filters + Actions) -->
            <div class="flex flex-wrap md:flex-nowrap items-center gap-2.5 w-full xl:w-auto justify-end">
                
                <!-- From Date -->
                <div class="flex items-center gap-1.5">
                    <label class="text-xs font-semibold text-gray-600 whitespace-nowrap">From:</label>
                    <input type="date" x-model="filters.from_date" 
                           class="w-36 text-xs border border-gray-300 rounded-lg px-2.5 py-1.5 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- To Date -->
                <div class="flex items-center gap-1.5">
                    <label class="text-xs font-semibold text-gray-600 whitespace-nowrap">To:</label>
                    <input type="date" x-model="filters.to_date" 
                        class="w-36 text-xs border border-gray-300 rounded-lg px-2.5 py-1.5 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Search Field -->
                <div class="w-40">
                    <input type="text" x-model="filters.search" @keyup.enter="page = 1; fetchAttendances()" 
                        placeholder="ID or Name..." 
                        class="w-full text-xs border border-gray-300 rounded-lg px-2.5 py-1.5 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Search Button -->
                <button @click="page = 1; fetchAttendances()" 
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs px-3.5 py-1.5 rounded-lg shadow-sm transition whitespace-nowrap">
                    Search
                </button>

                <!-- Reset Button -->
                <button @click="resetFilters()" 
                        class="px-2.5 py-1.5 text-xs text-gray-500 hover:text-red-600 border border-gray-300 rounded-lg font-bold" 
                        title="Reset Filters">✕</button>

                <div class="h-6 w-px bg-gray-200 hidden md:block"></div>

                <!-- Sync Device Button -->
                <button @click="syncDeviceData()" 
                        :disabled="syncing"
                        class="bg-emerald-600 hover:bg-emerald-800 text-white font-semibold px-3.5 py-1.5 rounded-lg shadow-sm transition flex items-center gap-1.5 text-xs whitespace-nowrap disabled:opacity-50">
                    <svg x-show="!syncing" class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.609-1.276z"/></svg>
                    <span x-text="syncing ? 'Syncing...' : 'Sync Device'"></span>
                </button>
            </div>

        </div>
    </div>

    <!-- Attendance Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-max">
            <thead>
                <tr class="bg-slate-50 border-b border-gray-200 text-gray-600 text-xs font-bold uppercase">
                    <th class="p-4">Date</th>
                    <th class="p-4">Employee</th>
                    <th class="p-4">In Time</th>
                    <th class="p-4">Out Time</th>
                    <th class="p-4">Total Hours</th>
                    <th class="p-4 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                <tr x-show="loading">
                    <td colspan="6" class="p-8 text-center text-slate-500 font-medium">Loading attendance records...</td>
                </tr>

                <template x-for="(item, index) in attendances" :key="index">
                    <tr class="hover:bg-slate-50/50 transition-colors" x-show="!loading">
                        <td class="p-4 font-mono text-slate-600" x-text="item.date"></td>
                        <td class="p-4">
                            <div class="font-semibold text-gray-800" x-text="item.employee_name || 'N/A'"></div>
                            <div class="text-xs text-gray-400" x-text="item.emp_id ? '#' + item.emp_id : ''"></div>
                        </td>
                        <td class="p-4 font-semibold" :class="item.in_time ? 'text-emerald-600' : 'text-slate-300'" x-text="item.in_time || '--:--'"></td>
                        <td class="p-4 font-semibold" :class="item.out_time ? 'text-rose-600' : 'text-slate-300'" x-text="item.out_time || '--:--'"></td>
                        <td class="p-4 font-mono text-slate-600" x-text="item.work_hours || '--'"></td>
                        <td class="p-4 text-center">
                            <template x-if="item.status">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full"
                                    :class="{
                                        'bg-emerald-100 text-emerald-700': item.status === 'Present',
                                        'bg-amber-100 text-amber-700': item.status === 'Late',
                                        'bg-rose-100 text-rose-700': item.status === 'Absent'
                                    }"
                                    x-text="item.status"></span>
                            </template>
                            <template x-if="!item.status">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-rose-600 text-slate-100">Absent</span>
                            </template>
                        </td>
                    </tr>
                </template>

                <tr x-show="!hasSearched && !loading" x-cloak>
                    <td colspan="6" class="p-8 text-center text-slate-400 font-medium">Select a date range and click "Search" to view attendance.</td>
                </tr>

                <tr x-show="hasSearched && attendances.length === 0 && !loading" x-cloak>
                    <td colspan="6" class="p-8 text-center text-slate-400 font-medium">No attendance logs found for selected criteria.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="flex justify-between items-center pt-2 text-xs font-semibold text-slate-500" x-show="hasSearched && attendances.length > 0">
        <div>Showing <span class="text-slate-800" x-text="attendances.length"></span> of <span class="text-slate-800" x-text="total"></span> records</div>
        <div class="flex items-center gap-2">
            <button @click="prevPage()" :disabled="page === 1 || loading" :class="page === 1 || loading ? 'opacity-50 cursor-not-allowed' : 'hover:bg-slate-100 text-slate-800'" class="px-3 py-1.5 border border-slate-200 rounded-lg transition">◀ Prev</button>
            <div class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-slate-700">Page <span x-text="page"></span> of <span x-text="lastPage"></span></div>
            <button @click="nextPage()" :disabled="page === lastPage || loading" :class="page === lastPage || loading ? 'opacity-50 cursor-not-allowed' : 'hover:bg-slate-100 text-slate-800'" class="px-3 py-1.5 border border-slate-200 rounded-lg transition">Next ▶</button>
        </div>
    </div>
</div>
@endsection