@extends('layouts.app')

@section('title', __('crm.add_appointment'))
@section('breadcrumb', __('crm.add_appointment'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('crm.add_appointment') }}</h1>
        <p class="page-subtitle">{{ __('crm.add_appointment_subtitle') }}</p>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('appointments.store') }}" method="POST">
    @csrf
    @include('crm.appointments._form')

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('crm.add_appointment') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
