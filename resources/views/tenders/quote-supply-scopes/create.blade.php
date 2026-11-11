@extends('layouts.app')

@section('title', __('tenders.add_quote_supply_scope'))
@section('breadcrumb', __('tenders.add_quote_supply_scope'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('tenders.add_quote_supply_scope') }}</h1>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('quote-supply-scopes.store') }}" method="POST">
    @csrf
    @include('tenders.quote-supply-scopes._form')

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('tenders.add_quote_supply_scope') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
