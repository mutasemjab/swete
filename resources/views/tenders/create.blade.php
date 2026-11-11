@extends('layouts.app')

@section('title', __('tenders.add_tender'))
@section('breadcrumb', __('tenders.add_tender'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('tenders.add_tender') }}</h1>
        <p class="page-subtitle">{{ __('tenders.add_tender_subtitle') }}</p>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('tenders.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="back" value="{{ $backUrl }}">
    @include('tenders._form', ['customers' => $customers, 'statuses' => $statuses, 'countries' => $countries, 'governorates' => $governorates])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('tenders.add_tender') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
