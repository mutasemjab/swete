@extends('layouts.app')

@section('title', __('maintenance.edit_device_password'))
@section('breadcrumb', __('maintenance.edit_device_password'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('maintenance.edit_device_password') }}</h1>
    </div>
    <a href="{{ route('maintenance-device-passwords.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('maintenance-device-passwords.update', $devicePassword) }}" method="POST">
    @csrf
    @method('PUT')
    @include('maintenance.device-passwords._form', ['devicePassword' => $devicePassword])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ route('maintenance-device-passwords.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
