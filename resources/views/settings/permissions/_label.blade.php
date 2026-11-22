<span class="block">
    <span class="block text-sm font-semibold text-slate-700">{{ \App\Support\PermissionLabels::permission($permission->name) }}</span>
    @if(in_array($permission->name, ['maintenance.requests.manage', 'maintenance.visits.manage'], true))
        <span class="block text-xs text-slate-500 mt-1">{{ __('permissions_ui.maintenance_manager_hint') }}</span>
    @endif
</span>
