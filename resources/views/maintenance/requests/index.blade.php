@extends('layouts.app')

@section('title', __('maintenance.requests_list'))
@section('breadcrumb', __('maintenance.requests_list'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('maintenance.requests_list') }}</h1>
        <p class="page-subtitle">{{ __('maintenance.requests_subtitle') }}</p>
    </div>
</div>

<div class="card mb-5 px-5 py-4">
    <form method="GET" class="flex items-center gap-3">
        <select name="status" class="form-select max-w-xs" onchange="this.form.submit()">
            <option value="">{{ __('app.all') }}</option>
            <option value="pending" @selected(request('status') === 'pending')>{{ __('maintenance.request_status_pending') }}</option>
            <option value="approved" @selected(request('status') === 'approved')>{{ __('maintenance.request_status_approved') }}</option>
            <option value="rejected" @selected(request('status') === 'rejected')>{{ __('maintenance.request_status_rejected') }}</option>
        </select>
    </form>
</div>

<div class="card overflow-hidden">
    @if($maintenanceRequests->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-bell text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('maintenance.no_requests') }}</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('maintenance.report_customer') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('maintenance.request_description') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('maintenance.request_preferred_date') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.status') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('approvals.requested_at') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($maintenanceRequests as $maintenanceRequest)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-4 font-bold text-slate-800">{{ $maintenanceRequest->customer?->localized_name }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ \Illuminate\Support\Str::limit($maintenanceRequest->description, 80) }}</td>
                        <td class="px-5 py-4 text-sm text-slate-500" dir="ltr">{{ $maintenanceRequest->preferred_date?->format('Y-m-d') ?? '—' }}</td>
                        <td class="px-5 py-4">
                            @if($maintenanceRequest->status === 'pending')
                                <span class="badge bg-amber-100 text-amber-700">{{ __('maintenance.request_status_pending') }}</span>
                            @elseif($maintenanceRequest->status === 'approved')
                                <span class="badge bg-emerald-100 text-emerald-700">{{ __('maintenance.request_status_approved') }}</span>
                            @else
                                <span class="badge bg-rose-100 text-rose-700">{{ __('maintenance.request_status_rejected') }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-500">{{ $maintenanceRequest->created_at->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4">{{ $maintenanceRequests->links() }}</div>
    @endif
</div>
@endsection
