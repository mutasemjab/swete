@extends('layouts.app')

@section('title', __('maintenance.visits_list'))
@section('breadcrumb', __('maintenance.visits_list'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('maintenance.visits_list') }}</h1>
        <p class="page-subtitle">{{ __('maintenance.visits_subtitle') }}</p>
    </div>
</div>

<div class="card mb-5 px-5 py-4">
    <form method="GET" class="flex items-center gap-3">
        <select name="status" class="form-select max-w-xs" onchange="this.form.submit()">
            <option value="">{{ __('app.all') }}</option>
            <option value="open" @selected(request('status') === 'open')>{{ __('maintenance.visit_status_open') }}</option>
            <option value="submitted_to_customer" @selected(request('status') === 'submitted_to_customer')>{{ __('maintenance.visit_status_submitted') }}</option>
            <option value="customer_signed" @selected(request('status') === 'customer_signed')>{{ __('maintenance.visit_status_signed') }}</option>
            <option value="closed" @selected(request('status') === 'closed')>{{ __('maintenance.visit_status_closed') }}</option>
        </select>
    </form>
</div>

<div class="card overflow-hidden">
    @if($visits->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-route text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('maintenance.no_visits') }}</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('maintenance.report_customer') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('maintenance.visit_technician') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('maintenance.visit_duration') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('maintenance.visit_type') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.status') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($visits as $visit)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-4 font-bold text-slate-800">
                            <a href="{{ route('maintenance-visits.show', $visit) }}" class="hover:text-indigo-600 hover:underline">{{ $visit->customer?->localized_name }}</a>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $visit->technician?->name }}</td>
                        <td class="px-5 py-4 text-sm text-slate-500" dir="ltr">{{ $visit->duration ?? '—' }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $visit->visitType?->localized_name ?? '—' }}</td>
                        <td class="px-5 py-4">
                            @include('maintenance.visits._status-badge', ['status' => $visit->status])
                        </td>
                        <td class="px-5 py-4 text-end">
                            <a href="{{ route('maintenance-visits.show', $visit) }}" class="btn-secondary btn-sm">
                                <i class="fa-solid fa-eye"></i> {{ __('app.view') }}
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4">{{ $visits->links() }}</div>
    @endif
</div>
@endsection
