@extends('layouts.app')

@section('title', __('tenders.add_quote'))
@section('breadcrumb', __('tenders.add_quote'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('tenders.add_quote') }}</h1>
        <p class="page-subtitle">{{ __('tenders.add_quote_subtitle') }}</p>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('price-quotes.store') }}" method="POST">
    @csrf
    <input type="hidden" name="back" value="{{ $backUrl }}">
    @if($tender)
        <input type="hidden" name="tender_id" value="{{ $tender->id }}">
    @endif
    @if($report)
        <input type="hidden" name="report_id" value="{{ $report->id }}">
    @endif

    @include('tenders.price-quotes._form')

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('tenders.add_quote') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
