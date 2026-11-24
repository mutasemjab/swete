@extends('layouts.app')

@section('title', __('maintenance.contract_equipment'))
@section('breadcrumb', __('maintenance.contract_equipment'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('maintenance.contract_equipment') }}</h1>
        <p class="page-subtitle">{{ $contract->number }} · {{ $contract->customer?->localized_name }}</p>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('maintenance-contracts.equipment.sync', $contract) }}" method="POST">
    @csrf
    <div class="card overflow-hidden mb-5">
        @if($units->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <p class="text-slate-800 font-bold">{{ __('maintenance.contract_equipment_no_units') }}</p>
                <a href="{{ route('equipment-units.create') }}" class="btn-primary mt-4">
                    <i class="fa-solid fa-plus"></i>
                    {{ __('equipment.add_unit') }}
                </a>
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($units as $unit)
                <label class="flex items-center gap-3 px-5 py-3.5 hover:bg-slate-50/50 cursor-pointer">
                    <input type="checkbox" name="unit_ids[]" value="{{ $unit->id }}" @checked(in_array($unit->id, $coveredIds))
                           class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-400">
                    <span class="font-semibold text-slate-700">{{ $unit->localized_name }}</span>
                    @if($unit->serial_number)<span class="text-xs text-slate-400" dir="ltr">({{ $unit->serial_number }})</span>@endif
                </label>
                @endforeach
            </div>
        @endif
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
