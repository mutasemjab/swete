@extends('layouts.app')

@section('title', __('tenders.edit_quote_delivery_term'))
@section('breadcrumb', __('tenders.edit_quote_delivery_term'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('tenders.edit_quote_delivery_term') }}</h1>
        <p class="page-subtitle">{{ $term->localized_name }}</p>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('quote-delivery-terms.update', $term) }}" method="POST">
    @csrf
    @method('PUT')
    @include('tenders.quote-delivery-terms._form')

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
