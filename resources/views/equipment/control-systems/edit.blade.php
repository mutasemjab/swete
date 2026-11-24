@extends('layouts.app')

@section('title', __('equipment.edit_control_system'))
@section('breadcrumb', __('equipment.edit_control_system'))

@php($backUrl = \App\Support\BackNavigation::url([$controlSystem], 'control-systems.index'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('equipment.edit_control_system') }}</h1>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('control-systems.update', $controlSystem) }}" method="POST">
    @csrf
    @method('PUT')
    @include('equipment.control-systems._form', ['controlSystem' => $controlSystem])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
