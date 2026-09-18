@extends('layouts.tenant')
@section('title', isset($user) ? 'Edit User' : 'User Entry')
@section('content')
@php
    $flattenedPages = collect();

    // 1. Default Features (Always included regardless of plan)
    foreach (config('menu.default_features') as $feature) {
        $flattenedPages->push((object)[
            'id'     => $feature['route'],
            'name'   => $feature['label'],
            'module' => 'Core Settings'
        ]);
    }

    // 2. Filter Menus based on Tenant's Plan Features
    foreach (config('menu.menus') as $menuKey => $menu) {
        
        // Step A: Check if module is disabled in config OR not included in Tenant's Plan
        if (isset($menu['enabled']) && $menu['enabled'] === false) continue;
        if (!in_array('*', $planFeatures) && !in_array($menuKey, $planFeatures)) continue;

        foreach ($menu['items'] as $item) {
            if (isset($item['enabled']) && $item['enabled'] === false) continue;

            // Step B: Nested Sub-items Support (e.g. Inventory Setup)
            if (isset($item['sub'])) {
                if (isset($item['sub']['enabled']) && $item['sub']['enabled'] === false) continue;

                foreach ($item['sub']['items'] as $subItem) {
                    if (isset($subItem['enabled']) && $subItem['enabled'] === false) continue;

                    $flattenedPages->push((object)[
                        'id'     => $subItem['route'],
                        'name'   => $menu['label'] . ' → ' . $item['sub']['label'] . ' → ' . $subItem['label'],
                        'module' => $menu['label']
                    ]);
                }
            } else {
                // Step C: Single Level Items
                $flattenedPages->push((object)[
                    'id'     => $item['route'],
                    'name'   => $menu['label'] . ' → ' . $item['label'],
                    'module' => $menu['label']
                ]);
            }
        }
    }

    // Grouping items by Module Name
    $groupedModules = $flattenedPages->groupBy('module');
@endphp
<div x-data="permissionMatrix()" class="space-y-6">
    {{-- Form Wrapping --}}
    <form action="{{ route('tenant.user.permission.save') }}" method="POST">
        @csrf
        @method('PUT')

        <input type="hidden" name="user_id" value="{{ $employee->id ?? '' }}" />

        @if($groupedModules->isNotEmpty())
            <!-- Overall Select All Top Bar -->
            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-xs font-semibold text-slate-700">Global Quick Actions:</span>
                    <label class="inline-flex items-center gap-2 text-xs text-slate-600 font-medium cursor-pointer select-none">
                        <input type="checkbox" 
                               x-model="selectAllGlobal" 
                               @change="toggleAllGlobal()" 
                               class="w-4 h-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500/20 focus:ring-2 cursor-pointer transition">
                        Select All Across All Modules
                    </label>
                </div>
                <span class="text-xs text-slate-400 font-mono" x-text="getSelectedCount() + ' permission(s) selected'"></span>
            </div>

            @foreach($groupedModules as $moduleName => $pages)
                @php
                    $moduleKey = Str::slug($moduleName, '_');
                @endphp

                <div x-data="{ 
                        moduleKey: '{{ $moduleKey }}',
                        toggleModuleAll(e) {
                            let checked = e.target.checked;
                            let inputs = $el.querySelectorAll('input[type=checkbox].perm-check');
                            inputs.forEach(i => {
                                i.checked = checked;
                                i.dispatchEvent(new Event('change'));
                            });
                        }
                     }" 
                     class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden mb-6">
                    
                    <!-- Module Header with Module-Wise Select All -->
                    <div class="px-5 py-3.5 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i data-lucide="layers" class="w-4 h-4 text-slate-500"></i>
                            <h3 class="text-sm font-semibold text-slate-800 tracking-wide">{{ $moduleName }}</h3>
                        </div>

                        <div class="flex items-center gap-4">
                            <!-- Module Select All Checkbox -->
                            <label class="inline-flex items-center gap-2 text-xs font-medium text-slate-600 cursor-pointer select-none">
                                <input type="checkbox" 
                                       @change="toggleModuleAll($event)"
                                       class="w-4 h-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500/20 focus:ring-2 cursor-pointer transition">
                                Select Module All
                            </label>

                            <span class="text-[11px] font-medium px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                                {{ count($pages) }} {{ Str::plural('Item', count($pages)) }}
                            </span>
                        </div>
                    </div>

                    <!-- Permissions Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-600">
                            <thead class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[11px] font-semibold border-b border-slate-100">
                                <tr>
                                    <th class="py-3 px-4 w-12 text-center">#</th>
                                    <th class="py-3 px-4">Module / Feature Page</th>
                                    <th class="py-3 px-3 text-center">Create</th>
                                    <th class="py-3 px-3 text-center">Read</th>
                                    <th class="py-3 px-3 text-center">Update</th>
                                    <th class="py-3 px-3 text-center">Delete</th>
                                    <th class="py-3 px-3 text-center">Cancel</th>
                                    <th class="py-3 px-3 text-center">Approval</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @foreach($pages as $index => $page)
                                    @php
                                        $perm = $userPermissions[$page->id] ?? null;
                                    @endphp
                                    <tr class="hover:bg-slate-50/60 transition-colors">
                                        <td class="py-3 px-4 text-center font-mono text-slate-400">{{ $loop->iteration }}</td>
                                        <td class="py-3 px-4 text-slate-800 font-semibold">{{ $page->name }}</td>

                                        {{-- Custom Styled Checkboxes --}}
                                        @php
                                            // DB Column Names according to your Schema
                                            $actionMap = [
                                                'create'   => 'create',
                                                'read'     => 'read',
                                                'update'   => 'update',
                                                'delete'   => 'delete',
                                                'cancel'   => 'cancle',  // Matches DB column 'cancle'
                                                'approval' => 'approve', // Matches DB column 'approve'
                                            ];
                                        @endphp
                                        @foreach($actionMap as $formAction => $dbColumn)
                                            <td class="py-3 px-3 text-center">
                                                <input type="checkbox" 
                                                    name="permissions[{{ $page->id }}][{{ $formAction }}]" 
                                                    value="1"
                                                    @change="updateCount()"
                                                    {{ !empty($perm->$dbColumn) && $perm->$dbColumn == 1 ? 'checked' : '' }}
                                                    class="perm-check w-4 h-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500/20 focus:ring-2 cursor-pointer transition">
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach

            <!-- Floating / Bottom Action Bar -->
            <div class="sticky bottom-4 bg-white/90 backdrop-blur-md p-4 rounded-xl border border-slate-200/80 shadow-lg flex items-center justify-between">
                <span class="text-xs text-slate-500 font-medium">
                    Changes will take effect immediately upon saving.
                </span>
                <div class="flex items-center gap-3">
                    <a href="{{ route('tenant.user.index') }}" 
                       class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 bg-white border border-slate-200 hover:bg-slate-50 rounded-lg transition shadow-sm">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-5 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-lg transition shadow-sm shadow-indigo-200">
                        Save Permissions
                    </button>
                </div>
            </div>
        @else
            <div class="p-8 text-center bg-white rounded-xl border border-slate-200">
                <i data-lucide="alert-circle" class="w-8 h-8 text-amber-500 mx-auto mb-2"></i>
                <p class="text-sm font-medium text-slate-600">No active modules found for this tenant's plan.</p>
            </div>
        @endif
    </form>
</div>
@endsection
@push('scripts')
<!-- Alpine Component Script -->
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('permissionMatrix', () => ({
            selectAllGlobal: false,
            selectedCount: 0,
            init() {
                this.updateCount();
            },
            toggleAllGlobal() {
                let checkboxes = document.querySelectorAll('input[type=checkbox].perm-check');
                checkboxes.forEach(cb => {
                    cb.checked = this.selectAllGlobal;
                });
                this.updateCount();
            },
            updateCount() {
                let checkedBoxes = document.querySelectorAll('input[type=checkbox].perm-check:checked');
                this.selectedCount = checkedBoxes.length;
            },
            getSelectedCount() {
                return this.selectedCount;
            }
        }));
    });
</script>
@endpush