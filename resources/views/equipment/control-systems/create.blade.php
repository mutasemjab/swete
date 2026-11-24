@extends('layouts.app')

@section('title', __('equipment.add_control_system'))
@section('breadcrumb', __('equipment.add_control_system'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('equipment.add_control_system') }}</h1>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('control-systems.store') }}" method="POST">
    @csrf
    @include('equipment.control-systems._form', ['controlSystem' => null])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('equipment.add_control_system') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
