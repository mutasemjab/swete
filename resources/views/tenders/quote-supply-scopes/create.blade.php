@extends('layouts.app')

@section('title', __('tenders.add_quote_supply_scope'))
@section('breadcrumb', __('tenders.add_quote_supply_scope'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('tenders.add_quote_supply_scope') }}</h1>
    </div>
    <a href="{{ route('quote-supply-scopes.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('quote-supply-scopes.store') }}" method="POST">
    @csrf
    @include('tenders.quote-supply-scopes._form')

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('tenders.add_quote_supply_scope') }}
        </button>
        <a href="{{ route('quote-supply-scopes.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
