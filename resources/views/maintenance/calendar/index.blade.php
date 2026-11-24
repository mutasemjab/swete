@extends('layouts.app')

@section('title', __('maintenance.calendar'))
@section('breadcrumb', __('maintenance.calendar'))

@php
    $prevDate = match($view) {
        'month' => $anchor->copy()->subMonth()->toDateString(),
        'week'  => $anchor->copy()->subDays(7)->toDateString(),
        'day'   => $anchor->copy()->subDay()->toDateString(),
        default => $anchor->toDateString(),
    };
    $nextDate = match($view) {
        'month' => $anchor->copy()->addMonth()->toDateString(),
        'week'  => $anchor->copy()->addDays(7)->toDateString(),
        'day'   => $anchor->copy()->addDay()->toDateString(),
        default => $anchor->toDateString(),
    };
    $typeColors = ['periodic' => 'blue', 'emergency' => 'rose'];
@endphp

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('maintenance.calendar') }}</h1>
        <p class="page-subtitle">{{ __('maintenance.calendar_subtitle') }}</p>
    </div>
    @can('maintenance.contract_scheduled_visits.create')
    <a href="{{ route('contract-scheduled-visits.index') }}" class="btn-secondary">
        <i class="fa-solid fa-list"></i>
        {{ __('maintenance.contract_scheduled_visits') }}
    </a>
    @endcan
</div>

<div class="card px-5 py-4 mb-5 flex flex-wrap items-center justify-between gap-3">
    <div class="flex items-center gap-2">
        <a href="{{ route('maintenance-calendar.index', array_merge(request()->except(['date']), ['view' => $view, 'date' => $prevDate])) }}" class="btn-secondary btn-sm"><i class="fa-solid fa-chevron-{{ app()->isLocale('ar') ? 'right' : 'left' }}"></i></a>
        <a href="{{ route('maintenance-calendar.index', array_merge(request()->except(['date']), ['view' => $view, 'date' => now()->toDateString()])) }}" class="btn-secondary btn-sm">{{ __('maintenance.calendar_today') }}</a>
        <a href="{{ route('maintenance-calendar.index', array_merge(request()->except(['date']), ['view' => $view, 'date' => $nextDate])) }}" class="btn-secondary btn-sm"><i class="fa-solid fa-chevron-{{ app()->isLocale('ar') ? 'left' : 'right' }}"></i></a>
        <span class="font-bold text-slate-700 ms-2" dir="ltr">{{ $anchor->translatedFormat('F Y') }}</span>
    </div>
    <div class="flex items-center gap-1">
        @foreach(['month', 'week', 'day', 'list'] as $v)
            <a href="{{ route('maintenance-calendar.index', array_merge(request()->except(['view']), ['view' => $v])) }}"
               class="px-3 py-1.5 rounded-lg text-sm font-semibold {{ $view === $v ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                {{ __('maintenance.calendar_view_'.$v) }}
            </a>
        @endforeach
    </div>
</div>

<form method="GET" class="card px-5 py-4 mb-5 flex flex-wrap gap-3">
    <input type="hidden" name="view" value="{{ $view }}">
    <input type="hidden" name="date" value="{{ $anchor->toDateString() }}">
    <select name="customer_id" class="js-select2 form-select w-48">
        <option value="">{{ __('maintenance.all_customers') }}</option>
        @foreach($customers as $customer)
            <option value="{{ $customer->id }}" @selected(request('customer_id') == $customer->id)>{{ $customer->localized_name }}</option>
        @endforeach
    </select>
    <select name="type" class="form-select w-40">
        <option value="">{{ __('maintenance.calendar_all_types') }}</option>
        <option value="periodic" @selected(request('type') == 'periodic')>{{ __('maintenance.scheduled_visit_type_periodic') }}</option>
        <option value="emergency" @selected(request('type') == 'emergency')>{{ __('maintenance.scheduled_visit_type_emergency') }}</option>
    </select>
    <label class="flex items-center gap-2 text-sm text-slate-600 px-2">
        <input type="checkbox" name="overdue_only" value="1" @checked(request()->boolean('overdue_only'))>
        {{ __('maintenance.calendar_overdue_only') }}
    </label>
    <button type="submit" class="btn-primary"><i class="fa-solid fa-filter"></i> {{ __('app.search') }}</button>
</form>

@php
    $renderChip = function ($visit) use ($typeColors) {
        $overdue = $visit->isOverdue();
        $color = $overdue ? 'rose' : ($typeColors[$visit->type] ?? 'slate');
        $label = ($visit->contract->number ?? '') . ($visit->unit ? ' · ' . $visit->unit->localized_name : '');
        return '<a href="' . route('maintenance-contracts.show', $visit->contract_id) . '" class="block truncate text-xs px-1.5 py-0.5 rounded bg-' . $color . '-100 text-' . $color . '-700 mb-1 hover:bg-' . $color . '-200" title="' . e($label) . '">' . e($label) . '</a>';
    };
@endphp

<div class="card overflow-hidden">
    @if($view === 'month')
        <div class="grid grid-cols-7 bg-slate-50 border-b border-slate-100">
            @foreach(['sun','mon','tue','wed','thu','fri','sat'] as $d)
                <div class="px-2 py-2 text-center text-xs font-black text-slate-500 uppercase">{{ __('maintenance.calendar_day_'.$d) }}</div>
            @endforeach
        </div>
        @foreach($weeks as $week)
        <div class="grid grid-cols-7 border-b border-slate-100 last:border-0">
            @foreach($week as $day)
            @php($dayVisits = $byDate->get($day->toDateString(), collect()))
            <div class="min-h-28 p-1.5 border-e border-slate-100 last:border-e-0 {{ $day->month !== $anchor->month ? 'bg-slate-50/50' : '' }} {{ $day->isToday() ? 'bg-indigo-50/50' : '' }}">
                <a href="{{ route('maintenance-calendar.index', ['view' => 'day', 'date' => $day->toDateString()]) }}"
                   class="text-xs font-bold {{ $day->month !== $anchor->month ? 'text-slate-300' : ($day->isToday() ? 'text-indigo-600' : 'text-slate-500') }}">{{ $day->day }}</a>
                <div class="mt-1">
                    @foreach($dayVisits->take(3) as $visit)
                        {!! $renderChip($visit) !!}
                    @endforeach
                    @if($dayVisits->count() > 3)
                        <span class="text-xs text-slate-400">+{{ $dayVisits->count() - 3 }}</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endforeach
    @elseif($view === 'week')
        <div class="grid grid-cols-7">
            @foreach($days as $day)
            @php($dayVisits = $byDate->get($day->toDateString(), collect()))
            <div class="min-h-40 p-2 border-e border-slate-100 last:border-e-0 {{ $day->isToday() ? 'bg-indigo-50/50' : '' }}">
                <p class="text-xs font-bold {{ $day->isToday() ? 'text-indigo-600' : 'text-slate-500' }} mb-2" dir="ltr">{{ $day->translatedFormat('D j') }}</p>
                @foreach($dayVisits as $visit)
                    {!! $renderChip($visit) !!}
                @endforeach
            </div>
            @endforeach
        </div>
    @elseif($view === 'day')
        <div class="px-6 py-5">
            <p class="font-bold text-slate-700 mb-4" dir="ltr">{{ $anchor->translatedFormat('l, F j Y') }}</p>
            @php($dayVisits = $byDate->get($anchor->toDateString(), collect()))
            @forelse($dayVisits as $visit)
                <div class="py-2 border-b border-slate-100 last:border-0">{!! $renderChip($visit) !!}</div>
            @empty
                <p class="text-sm text-slate-400">{{ __('maintenance.calendar_no_visits') }}</p>
            @endforelse
        </div>
    @else
        <div class="divide-y divide-slate-100">
            @forelse($visits as $visit)
                <div class="px-6 py-3 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="font-mono text-sm text-slate-500" dir="ltr">{{ $visit->scheduled_date->format('Y-m-d') }}</span>
                        <a href="{{ route('maintenance-contracts.show', $visit->contract_id) }}" class="font-bold text-slate-800 hover:text-indigo-600">{{ $visit->contract->number }}</a>
                        @if($visit->unit)<span class="text-sm text-slate-500">{{ $visit->unit->localized_name }}</span>@endif
                        <span class="badge bg-{{ $typeColors[$visit->type] ?? 'slate' }}-100 text-{{ $typeColors[$visit->type] ?? 'slate' }}-700">{{ __('maintenance.scheduled_visit_type_'.$visit->type) }}</span>
                        @if($visit->isOverdue())<span class="badge bg-rose-100 text-rose-700">{{ __('maintenance.visit_overdue') }}</span>@endif
                    </div>
                </div>
            @empty
                <p class="px-6 py-10 text-sm text-slate-400 text-center">{{ __('maintenance.calendar_no_visits') }}</p>
            @endforelse
        </div>
    @endif
</div>
@endsection
