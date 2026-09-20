@extends('layouts.app')

@section('title', __('crm.appointments_list'))
@section('breadcrumb', __('crm.appointments'))

@section('content')
@php $filterKeys = ['search', 'status', 'appointment_type_id', 'assigned_to', 'date_from', 'date_to']; @endphp

<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('crm.appointments_list') }}</h1>
        <p class="page-subtitle">{{ __('crm.appointments_subtitle') }}</p>
    </div>
    <a href="{{ route('appointments.create') }}" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        {{ __('crm.add_appointment') }}
    </a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="card px-5 py-4 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-rose-100 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-calendar-days text-rose-600"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800">{{ $appointments->total() }}</p>
            <p class="text-xs text-slate-500 font-medium mt-0.5">{{ __('crm.total_appointments') }}</p>
        </div>
    </div>
</div>

<form method="GET" action="{{ route('appointments.index') }}" class="card px-5 py-4 mb-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
    <div>
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute start-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="{{ __('crm.appointment_title') }}..." class="form-input ps-10">
        </div>
    </div>
    <div>
        <select name="status" class="form-select w-full">
            <option value="">{{ __('app.all_statuses') }}</option>
            @foreach(\App\Models\Appointment::STATUSES as $statusOption)
                <option value="{{ $statusOption }}" @selected(request('status') === $statusOption)>{{ __('crm.status_' . $statusOption) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <select name="appointment_type_id" class="js-select2 form-select w-full">
            <option value="">{{ __('crm.all_types') }}</option>
            @foreach($types as $type)
                <option value="{{ $type->id }}" @selected(request('appointment_type_id') == $type->id)>{{ $type->localized_name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <select name="assigned_to" class="js-select2 form-select w-full">
            <option value="">{{ __('crm.all_employees') }}</option>
            @foreach($employees as $employee)
                <option value="{{ $employee->id }}" @selected(request('assigned_to') == $employee->id)>{{ $employee->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input w-full" title="{{ __('app.from') }}">
    </div>
    <div>
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input w-full" title="{{ __('app.to') }}">
    </div>
    <div class="flex items-center gap-2">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-filter"></i>
            {{ __('app.search') }}
        </button>
        @if(request()->hasAny($filterKeys))
            <a href="{{ route('appointments.index') }}" class="btn-secondary">
                <i class="fa-solid fa-xmark"></i>
                {{ __('app.clear_filters') }}
            </a>
        @endif
    </div>
</form>

<div class="card overflow-hidden">
    @if($appointments->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-calendar-days text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">
                {{ request()->hasAny($filterKeys) ? __('crm.no_appointments_search') : __('crm.no_appointments') }}
            </p>
            @if(!request()->hasAny($filterKeys))
                <a href="{{ route('appointments.create') }}" class="btn-primary mt-5">
                    <i class="fa-solid fa-plus"></i>
                    {{ __('crm.add_first_appointment') }}
                </a>
            @endif
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('crm.appointment_title') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('crm.appointment_date') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('crm.appointment_type') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden md:table-cell">{{ __('crm.appointment_customer') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden md:table-cell">{{ __('crm.appointment_assigned_to') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('crm.appointment_status') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($appointments as $appointment)
                    @php
                        $dueToday = $appointment->isDueToday();
                        $overdue  = $appointment->isOverdue();
                        $rowClass = $dueToday ? 'bg-amber-50 hover:bg-amber-100' : ($overdue ? 'bg-rose-50 hover:bg-rose-100' : 'hover:bg-slate-50/50');
                    @endphp
                    <tr class="transition-colors group {{ $rowClass }}">
                        <td class="px-5 py-4">
                            <p class="font-bold text-slate-800">{{ $appointment->title }}</p>
                            @if($appointment->notes)
                                <p class="text-xs text-slate-400 mt-0.5 line-clamp-1">{{ $appointment->notes }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600 whitespace-nowrap">
                            <span dir="ltr">{{ $appointment->appointment_date->format('Y-m-d') }}</span>
                            @if($dueToday)
                                <span class="badge bg-amber-100 text-amber-700 ms-1" title="{{ __('crm.due_today_hint') }}">
                                    <i class="fa-solid fa-bell"></i> {{ __('crm.due_today') }}
                                </span>
                            @elseif($overdue)
                                <span class="badge bg-rose-100 text-rose-700 ms-1" title="{{ __('crm.overdue_hint') }}">
                                    <i class="fa-solid fa-triangle-exclamation"></i> {{ __('crm.overdue') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4"><span class="badge bg-rose-100 text-rose-700">{{ $appointment->type?->localized_name }}</span></td>
                        <td class="px-5 py-4 hidden md:table-cell text-sm text-slate-600">{{ $appointment->customer?->localized_name ?? '—' }}</td>
                        <td class="px-5 py-4 hidden md:table-cell text-sm text-slate-600">{{ $appointment->assignee?->name }}</td>
                        <td class="px-5 py-4">
                            @if($appointment->status === 'completed')
                                <span class="badge bg-emerald-100 text-emerald-700">{{ __('crm.status_completed') }}</span>
                            @else
                                <span class="badge bg-indigo-100 text-indigo-700">{{ __('crm.status_scheduled') }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <form action="{{ route('appointments.toggle-complete', $appointment) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            title="{{ $appointment->status === 'completed' ? __('crm.reopen') : __('crm.mark_completed') }}"
                                            class="p-1.5 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all">
                                        <i class="fa-solid {{ $appointment->status === 'completed' ? 'fa-rotate-left' : 'fa-check' }} text-sm"></i>
                                    </button>
                                </form>
                                <a href="{{ route('appointments.edit', $appointment) }}"
                                   class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="{{ __('app.edit') }}">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                <button type="button" title="{{ __('app.delete') }}"
                                        @click="$dispatch('delete-confirm', { action: '{{ route('appointments.destroy', $appointment) }}', message: '{{ __('app.delete_confirm_msg') }}' })"
                                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($appointments->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                {{ __('app.showing') }} <span class="font-bold text-slate-700">{{ $appointments->firstItem() }}</span>
                {{ __('app.to') }} <span class="font-bold text-slate-700">{{ $appointments->lastItem() }}</span>
                {{ __('app.of') }} <span class="font-bold text-slate-700">{{ $appointments->total() }}</span>
                {{ __('app.results') }}
            </p>
            <div class="flex gap-1">
                @if($appointments->onFirstPage())
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.previous') }}</span>
                @else
                    <a href="{{ $appointments->previousPageUrl() }}" class="btn-secondary btn-sm">{{ __('app.previous') }}</a>
                @endif
                @if($appointments->hasMorePages())
                    <a href="{{ $appointments->nextPageUrl() }}" class="btn-primary btn-sm">{{ __('app.next') }}</a>
                @else
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.next') }}</span>
                @endif
            </div>
        </div>
        @endif
    @endif
</div>
@endsection
