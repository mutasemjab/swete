@extends('layouts.app')

@section('title', __('maintenance.contract_scheduled_visits'))
@section('breadcrumb', __('maintenance.contract_scheduled_visits'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('maintenance.contract_scheduled_visits') }}</h1>
        <p class="page-subtitle">{{ __('maintenance.contracts_subtitle') }}</p>
    </div>
    @if($dueCount > 0)
        <a href="{{ route('contract-scheduled-visits.index', ['due' => 1]) }}" class="badge bg-rose-100 text-rose-700 !text-sm !px-4 !py-2">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ __('maintenance.scheduled_visits_due_count', ['count' => $dueCount]) }}
        </a>
    @endif
</div>

<div class="card overflow-hidden">
    @if($scheduledVisits->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-calendar-days text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('maintenance.no_scheduled_visits') }}</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('maintenance.contract_number') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('maintenance.contract_customer') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('maintenance.scheduled_visit_date') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('maintenance.scheduled_visit_type') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($scheduledVisits as $scheduledVisit)
                    <tr class="hover:bg-slate-50/50 transition-colors @if($scheduledVisit->isOverdue()) bg-rose-50/60 @elseif($scheduledVisit->isDueToday()) bg-amber-50/60 @endif">
                        <td class="px-5 py-4">
                            <a href="{{ route('maintenance-contracts.show', $scheduledVisit->contract) }}" class="font-mono font-bold text-slate-700 hover:text-indigo-600 transition-colors">{{ $scheduledVisit->contract?->number }}</a>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $scheduledVisit->contract?->customer?->localized_name }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">
                            <span dir="ltr">{{ $scheduledVisit->scheduled_date->format('Y-m-d') }}</span>
                            @if($scheduledVisit->isOverdue())
                                <span class="badge bg-rose-100 text-rose-700 ms-1.5">{{ __('maintenance.visit_overdue') }}</span>
                            @elseif($scheduledVisit->isDueToday())
                                <span class="badge bg-amber-100 text-amber-700 ms-1.5">{{ __('maintenance.visit_due_today') }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <span class="badge {{ $scheduledVisit->type === 'emergency' ? 'bg-rose-100 text-rose-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ __('maintenance.scheduled_visit_type_' . $scheduledVisit->type) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4">{{ $scheduledVisits->links() }}</div>
    @endif
</div>
@endsection
