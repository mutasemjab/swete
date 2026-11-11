@extends('layouts.app')

@section('title', __('tenders.edit_status'))
@section('breadcrumb', __('tenders.edit_status'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('tenders.edit_status') }}</h1>
        <p class="page-subtitle">{{ $tenderStatus->localized_name }}</p>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('tender-statuses.update', $tenderStatus) }}" method="POST">
    @csrf
    @method('PUT')
    @include('tenders.statuses._form', ['colors' => $colors, 'tenderStatus' => $tenderStatus])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
