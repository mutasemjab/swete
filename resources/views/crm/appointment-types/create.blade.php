@extends('layouts.app')

@section('title', __('crm.add_appointment_type'))
@section('breadcrumb', __('crm.add_appointment_type'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('crm.add_appointment_type') }}</h1>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('appointment-types.store') }}" method="POST">
    @csrf
    @include('crm.appointment-types._form')

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('crm.add_appointment_type') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
