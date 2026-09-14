@extends('layouts.app')

@section('title', __('tenders.reminders_list'))
@section('breadcrumb', __('tenders.reminders_list'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('tenders.reminders_list') }}</h1>
        <p class="page-subtitle">{{ __('tenders.reminders_subtitle') }}</p>
    </div>
    <a href="{{ route('purchase-request-reminders.create') }}" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        {{ __('tenders.add_reminder') }}
    </a>
</div>

<div class="card overflow-hidden">
    @if($reminders->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-bell text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('tenders.no_reminders') }}</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.project') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('approvals.requested_by') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.status') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('approvals.requested_at') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($reminders as $reminder)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <a href="{{ route('purchase-request-reminders.show', $reminder) }}" class="font-bold text-slate-800 hover:text-indigo-600 hover:underline">
                                {{ $reminder->project?->number }} — {{ $reminder->project?->localized_title }}
                            </a>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $reminder->requester?->name }}</td>
                        <td class="px-5 py-4">
                            @if($reminder->status === 'fulfilled')
                                <span class="badge bg-emerald-100 text-emerald-700">{{ __('tenders.reminder_status_fulfilled') }}</span>
                            @else
                                <span class="badge bg-amber-100 text-amber-700">{{ __('tenders.reminder_status_pending') }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-500">{{ $reminder->created_at->diffForHumans() }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('purchase-request-reminders.show', $reminder) }}"
                                   class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="{{ __('app.view') }}">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($reminders->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                {{ __('app.showing') }} <span class="font-bold text-slate-700">{{ $reminders->firstItem() }}</span>
                {{ __('app.to') }} <span class="font-bold text-slate-700">{{ $reminders->lastItem() }}</span>
                {{ __('app.of') }} <span class="font-bold text-slate-700">{{ $reminders->total() }}</span>
                {{ __('app.results') }}
            </p>
            <div class="flex gap-1">
                @if($reminders->onFirstPage())
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.previous') }}</span>
                @else
                    <a href="{{ $reminders->previousPageUrl() }}" class="btn-secondary btn-sm">{{ __('app.previous') }}</a>
                @endif
                @if($reminders->hasMorePages())
                    <a href="{{ $reminders->nextPageUrl() }}" class="btn-primary btn-sm">{{ __('app.next') }}</a>
                @else
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.next') }}</span>
                @endif
            </div>
        </div>
        @endif
    @endif
</div>
@endsection
