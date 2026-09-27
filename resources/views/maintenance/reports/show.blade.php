@extends('layouts.app')

@section('title', $report->number)
@section('breadcrumb', $report->number)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $report->number }}</h1>
        <p class="page-subtitle">{{ $report->template_name }}</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('maintenance-reports.edit', $report) }}" class="btn-secondary">
            <i class="fa-solid fa-pen"></i>
            {{ __('app.edit') }}
        </a>
        <a href="{{ route('maintenance-reports.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
            {{ __('app.back_to_list') }}
        </a>
    </div>
</div>

<div class="card px-6 py-5 mb-5">
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('maintenance.report_customer') }}</dt>
            <dd class="font-bold text-slate-800">{{ $report->customer?->localized_name }} ({{ $report->customer?->code }})</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('maintenance.report_product') }}</dt>
            <dd class="font-bold text-slate-800">{{ $report->material?->localized_name ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('maintenance.report_date') }}</dt>
            <dd class="font-bold text-slate-800">{{ $report->date->format('Y-m-d') }}</dd>
        </div>
        @if($report->notes)
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('maintenance.report_notes') }}</dt>
            <dd class="text-slate-700">{{ $report->notes }}</dd>
        </div>
        @endif
    </dl>
</div>

<div class="card overflow-hidden">
    <div class="card-header">
        <h3 class="font-bold text-slate-700">{{ __('maintenance.template_fields') }}</h3>
    </div>
    <div class="divide-y divide-slate-100">
        @foreach($report->fields as $field)
        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
            <dt class="text-slate-500 font-medium sm:col-span-2">{{ $field->localized_question }}</dt>
            <dd class="font-bold text-slate-800">
                {{ $field->formatted_answer ?? __('maintenance.answer_not_answered') }}
            </dd>
        </div>
        @endforeach
    </div>
</div>
@endsection
