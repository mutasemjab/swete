@extends('layouts.app')

@section('title', $equipmentUnit->localized_name)
@section('breadcrumb', $equipmentUnit->localized_name)

@php($backUrl = \App\Support\BackNavigation::url([], 'equipment-units.index'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $equipmentUnit->localized_name }}</h1>
        <p class="page-subtitle">{{ $equipmentUnit->customer?->localized_name }}</p>
    </div>
    <div class="flex items-center gap-2">
        @can('equipment.units.edit')
        <a href="{{ route('equipment-units.edit', $equipmentUnit) }}" class="btn-secondary">
            <i class="fa-solid fa-pen"></i>
            {{ __('app.edit') }}
        </a>
        @endcan
        <a href="{{ route('superheat-calculator.index', ['equipment_unit_id' => $equipmentUnit->id]) }}" class="btn-secondary">
            <i class="fa-solid fa-calculator"></i>
            {{ __('equipment.superheat_calculator') }}
        </a>
        @include('components.back-link', ['url' => $backUrl])
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
    <div class="card px-6 py-5 lg:col-span-1">
        <h3 class="font-bold text-slate-700 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-circle-info text-cyan-500 text-sm"></i>
            {{ __('equipment.unit') }}
        </h3>
        <dl class="space-y-3 text-sm">
            <div class="flex justify-between gap-3"><dt class="text-slate-500">{{ __('equipment.category') }}</dt><dd class="font-semibold text-slate-700 text-end">{{ $equipmentUnit->category?->localized_name }}</dd></div>
            @if($equipmentUnit->subtype)
            <div class="flex justify-between gap-3"><dt class="text-slate-500">{{ __('equipment.subtype') }}</dt><dd class="font-semibold text-slate-700 text-end">{{ $equipmentUnit->subtype->localized_name }}</dd></div>
            @endif
            <div class="flex justify-between gap-3"><dt class="text-slate-500">{{ __('equipment.manufacturer') }}</dt><dd class="font-semibold text-slate-700 text-end">{{ $equipmentUnit->model?->manufacturer?->localized_name }}</dd></div>
            <div class="flex justify-between gap-3"><dt class="text-slate-500">{{ __('equipment.model') }}</dt><dd class="font-semibold text-slate-700 text-end" dir="ltr">{{ $equipmentUnit->model?->model_number }}</dd></div>
            @if($equipmentUnit->serial_number)
            <div class="flex justify-between gap-3"><dt class="text-slate-500">{{ __('equipment.unit_serial_number') }}</dt><dd class="font-semibold text-slate-700 text-end" dir="ltr">{{ $equipmentUnit->serial_number }}</dd></div>
            @endif
            @if($equipmentUnit->location)
            <div class="flex justify-between gap-3"><dt class="text-slate-500">{{ __('equipment.unit_location') }}</dt><dd class="font-semibold text-slate-700 text-end">{{ $equipmentUnit->location }}</dd></div>
            @endif
            @if($equipmentUnit->project)
            <div class="flex justify-between gap-3"><dt class="text-slate-500">{{ __('equipment.unit_project') }}</dt><dd class="font-semibold text-slate-700 text-end">{{ $equipmentUnit->project }}</dd></div>
            @endif
            <div class="flex justify-between gap-3"><dt class="text-slate-500">{{ __('equipment.unit_status') }}</dt><dd class="font-semibold text-slate-700 text-end">{{ __('equipment.unit_status_'.$equipmentUnit->status) }}</dd></div>
        </dl>
        @if($equipmentUnit->notes)
        <p class="text-sm text-slate-500 mt-4 pt-4 border-t border-slate-100">{{ $equipmentUnit->notes }}</p>
        @endif
    </div>

    <div class="card px-6 py-5 lg:col-span-1 flex flex-col items-center text-center">
        <h3 class="font-bold text-slate-700 mb-4 flex items-center gap-2 self-start">
            <i class="fa-solid fa-qrcode text-cyan-500 text-sm"></i>
            {{ __('equipment.unit_qr') }}
        </h3>
        <img src="{{ route('equipment-units.qr', $equipmentUnit) }}" alt="QR" class="w-40 h-40 border border-slate-100 rounded-xl p-2">
        <p class="text-xs text-slate-400 mt-3">{{ __('equipment.unit_qr_hint') }}</p>
        <a href="{{ route('equipment-units.qr', $equipmentUnit) }}" download="QR-{{ $equipmentUnit->id }}.svg" class="btn-secondary btn-sm mt-3">
            <i class="fa-solid fa-download"></i> {{ __('app.download') }}
        </a>
    </div>

    <div class="card px-6 py-5 lg:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-file-lines text-cyan-500 text-sm"></i>
                {{ __('equipment.unit_documents') }}
            </h3>
            @can('equipment.documents.upload')
            <a href="{{ route('documents.create') }}" class="text-sm text-indigo-600 hover:underline font-semibold">
                <i class="fa-solid fa-plus"></i> {{ __('equipment.add_document') }}
            </a>
            @endcan
        </div>

        @if($suggestedTemplate)
            <div class="flex items-center justify-between gap-3 mb-4 pb-4 border-b border-slate-100">
                <p class="text-sm text-slate-600">
                    {{ __('equipment.unit_suggested_template') }}: <span class="font-bold text-slate-800">{{ $suggestedTemplate->localized_name }}</span>
                </p>
                @can('maintenance.reports.create')
                <a href="{{ route('maintenance-reports.create', ['template_id' => $suggestedTemplate->id, 'equipment_unit_id' => $equipmentUnit->id]) }}" class="btn-primary btn-sm">
                    <i class="fa-solid fa-plus"></i> {{ __('equipment.unit_start_report') }}
                </a>
                @endcan
            </div>
        @endif

        @if($documents->isEmpty())
            <p class="text-sm text-slate-400 py-6 text-center">{{ __('equipment.unit_no_documents') }}</p>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($documents as $document)
                <div class="py-3 flex items-center justify-between gap-3">
                    <div>
                        <a href="{{ $document->url }}" target="_blank" class="font-semibold text-slate-700 hover:text-indigo-600 transition-colors">
                            <i class="fa-solid {{ $document->can_preview ? 'fa-eye' : 'fa-download' }} text-xs text-slate-400"></i>
                            {{ $document->localized_name }}
                        </a>
                        <p class="text-xs text-slate-400">{{ $document->type?->localized_name }} · {{ $document->source_label }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
