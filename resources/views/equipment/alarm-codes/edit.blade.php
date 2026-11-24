@extends('layouts.app')

@section('title', __('equipment.edit_alarm_code'))
@section('breadcrumb', __('equipment.edit_alarm_code'))

@php($backUrl = \App\Support\BackNavigation::url([$alarm], 'alarm-codes.index'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('equipment.edit_alarm_code') }}</h1>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('alarm-codes.update', $alarm) }}" method="POST">
    @csrf
    @method('PUT')
    @include('equipment.alarm-codes._form', ['alarm' => $alarm])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
