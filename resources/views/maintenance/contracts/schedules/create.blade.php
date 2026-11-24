@extends('layouts.app')

@section('title', __('maintenance.add_schedule'))
@section('breadcrumb', __('maintenance.add_schedule'))

@php($backUrl = \App\Support\BackNavigation::url([$contract], 'maintenance-contracts.show'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('maintenance.add_schedule') }}</h1>
        <p class="page-subtitle">{{ $contract->number }}</p>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('maintenance-contract-schedules.store', $contract) }}" method="POST">
    @csrf
    @include('maintenance.contracts.schedules._form', ['schedule' => null])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('maintenance.add_schedule') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
