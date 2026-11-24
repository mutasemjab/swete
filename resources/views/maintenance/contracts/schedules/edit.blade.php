@extends('layouts.app')

@section('title', __('maintenance.edit_schedule'))
@section('breadcrumb', __('maintenance.edit_schedule'))

@php($backUrl = \App\Support\BackNavigation::url([$contract], 'maintenance-contracts.show'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('maintenance.edit_schedule') }}</h1>
        <p class="page-subtitle">{{ $contract->number }}</p>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('maintenance-contract-schedules.update', [$contract, $schedule]) }}" method="POST">
    @csrf
    @method('PUT')
    @include('maintenance.contracts.schedules._form', ['schedule' => $schedule])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
