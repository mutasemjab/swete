@extends('layouts.app')

@section('title', __('maintenance.add_device_password'))
@section('breadcrumb', __('maintenance.add_device_password'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('maintenance.add_device_password') }}</h1>
    </div>
    <a href="{{ route('maintenance-device-passwords.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('maintenance-device-passwords.store') }}" method="POST">
    @csrf
    @include('maintenance.device-passwords._form', ['devicePassword' => null])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('maintenance.add_device_password') }}
        </button>
        <a href="{{ route('maintenance-device-passwords.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
