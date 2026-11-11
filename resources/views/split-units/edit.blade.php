@extends('layouts.app')

@section('title', __('split_units.edit'))
@section('breadcrumb', __('split_units.edit'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('split_units.edit') }}</h1>
        <p class="page-subtitle">{{ $splitUnit->number }}</p>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('split-units.update', $splitUnit) }}" method="POST">
    @csrf
    <input type="hidden" name="back" value="{{ $backUrl }}">
    @method('PUT')

    @include('split-units._form')

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
