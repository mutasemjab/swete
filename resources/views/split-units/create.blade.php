@extends('layouts.app')

@section('title', __('split_units.add'))
@section('breadcrumb', __('split_units.add'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('split_units.add') }}</h1>
        <p class="page-subtitle">{{ __('split_units.add_subtitle') }}</p>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('split-units.store') }}" method="POST">
    @csrf
    <input type="hidden" name="back" value="{{ $backUrl }}">

    @include('split-units._form')

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('split_units.add') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
