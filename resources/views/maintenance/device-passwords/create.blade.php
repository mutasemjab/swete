@extends('layouts.app')

@section('title', __('maintenance.add_device_password'))
@section('breadcrumb', __('maintenance.add_device_password'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('maintenance.add_device_password') }}</h1>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('maintenance-device-passwords.store') }}" method="POST">
    @csrf
    @include('maintenance.device-passwords._form', ['devicePassword' => null])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('maintenance.add_device_password') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
