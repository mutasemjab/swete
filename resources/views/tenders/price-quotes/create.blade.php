@extends('layouts.app')

@section('title', __('tenders.add_quote'))
@section('breadcrumb', __('tenders.add_quote'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('tenders.add_quote') }}</h1>
        <p class="page-subtitle">{{ __('tenders.add_quote_subtitle') }}</p>
    </div>
    <a href="{{ $tender ? route('tenders.show', $tender) : route('price-quotes.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('price-quotes.store') }}" method="POST">
    @csrf
    @if($tender)
        <input type="hidden" name="tender_id" value="{{ $tender->id }}">
    @endif

    @include('tenders.price-quotes._form')

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('tenders.add_quote') }}
        </button>
        <a href="{{ $tender ? route('tenders.show', $tender) : route('price-quotes.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
