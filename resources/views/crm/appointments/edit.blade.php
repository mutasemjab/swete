@extends('layouts.app')

@section('title', __('crm.edit_appointment'))
@section('breadcrumb', __('crm.edit_appointment'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('crm.edit_appointment') }}</h1>
        <p class="page-subtitle">{{ $appointment->title }}</p>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('appointments.update', $appointment) }}" method="POST">
    @csrf
    @method('PUT')
    @include('crm.appointments._form')

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
