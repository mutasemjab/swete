@extends('layouts.app')

@section('title', __('settings.activity_log_list'))
@section('breadcrumb', __('settings.activity_log'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('settings.activity_log_list') }}</h1>
        <p class="page-subtitle">{{ __('settings.activity_log_subtitle') }}</p>
    </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="card px-5 py-4 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-clock-rotate-left text-indigo-600"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800">{{ $activities->total() }}</p>
            <p class="text-xs text-slate-500 font-medium mt-0.5">{{ __('settings.total_activities') }}</p>
        </div>
    </div>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('settings.activity-log.index') }}" class="card px-5 py-4 mb-4 flex flex-wrap gap-3">
    <select name="causer_id" class="form-select w-48">
        <option value="">{{ __('settings.activity_all_users') }}</option>
        @foreach($causers as $causer)
            <option value="{{ $causer->id }}" @selected(request('causer_id') == $causer->id)>{{ $causer->name }}</option>
        @endforeach
    </select>
    <select name="subject_type" class="form-select w-48">
        <option value="">{{ __('settings.activity_all_subjects') }}</option>
        @foreach($subjectTypes as $subjectType)
            <option value="{{ $subjectType }}" @selected(request('subject_type') === $subjectType)>{{ class_basename($subjectType) }}</option>
        @endforeach
    </select>
    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input w-40" placeholder="{{ __('settings.activity_from') }}">
    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input w-40" placeholder="{{ __('settings.activity_to') }}">
    <button type="submit" class="btn-primary">
        <i class="fa-solid fa-filter"></i>
        {{ __('settings.activity_filter') }}
    </button>
    @if(request()->hasAny(['causer_id','subject_type','date_from','date_to']))
        <a href="{{ route('settings.activity-log.index') }}" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            {{ __('app.clear_filters') }}
        </a>
    @endif
</form>

<div class="card overflow-hidden">
    @if($activities->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-clock-rotate-left text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('settings.no_activity') }}</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('settings.activity_user') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('settings.activity_action') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('settings.activity_subject') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('settings.activity_date') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($activities as $activity)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <span class="font-bold text-slate-800">{{ $activity->causer?->name ?? __('settings.no_role') }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="badge bg-indigo-100 text-indigo-700">
                                {{ __('settings.event_' . $activity->description) !== 'settings.event_' . $activity->description ? __('settings.event_' . $activity->description) : $activity->description }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-sm text-slate-600">{{ $activity->subject_type ? class_basename($activity->subject_type) . ' #' . $activity->subject_id : '—' }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-sm text-slate-500">{{ $activity->created_at->diffForHumans() }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($activities->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                {{ __('app.showing') }} <span class="font-bold text-slate-700">{{ $activities->firstItem() }}</span>
                {{ __('app.to') }} <span class="font-bold text-slate-700">{{ $activities->lastItem() }}</span>
                {{ __('app.of') }} <span class="font-bold text-slate-700">{{ $activities->total() }}</span>
                {{ __('app.results') }}
            </p>
            <div class="flex gap-1">
                @if($activities->onFirstPage())
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.previous') }}</span>
                @else
                    <a href="{{ $activities->previousPageUrl() }}" class="btn-secondary btn-sm">{{ __('app.previous') }}</a>
                @endif

                @if($activities->hasMorePages())
                    <a href="{{ $activities->nextPageUrl() }}" class="btn-primary btn-sm">{{ __('app.next') }}</a>
                @else
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.next') }}</span>
                @endif
            </div>
        </div>
        @endif
    @endif
</div>
@endsection
