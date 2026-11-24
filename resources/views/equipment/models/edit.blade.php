@extends('layouts.app')

@section('title', __('equipment.edit_model'))
@section('breadcrumb', __('equipment.edit_model'))

@php($backUrl = \App\Support\BackNavigation::url([$model], 'equipment-models.index'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('equipment.edit_model') }}</h1>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('equipment-models.update', $model) }}" method="POST">
    @csrf
    @method('PUT')
    @include('equipment.models._form', ['model' => $model])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
