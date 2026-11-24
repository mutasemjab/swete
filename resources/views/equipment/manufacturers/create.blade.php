@extends('layouts.app')

@section('title', __('equipment.add_manufacturer'))
@section('breadcrumb', __('equipment.add_manufacturer'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('equipment.add_manufacturer') }}</h1>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('equipment-manufacturers.store') }}" method="POST">
    @csrf
    @include('equipment.manufacturers._form', ['manufacturer' => null])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('equipment.add_manufacturer') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
