@extends('layouts.app')

@section('title', __('equipment.add_alarm_code'))
@section('breadcrumb', __('equipment.add_alarm_code'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('equipment.add_alarm_code') }}</h1>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('alarm-codes.store') }}" method="POST">
    @csrf
    @include('equipment.alarm-codes._form', ['alarm' => null])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('equipment.add_alarm_code') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
