@extends('layouts.app')

@section('title', __('tenders.edit_quote'))
@section('breadcrumb', __('tenders.edit_quote'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('tenders.edit_quote') }}</h1>
        <p class="page-subtitle">{{ $priceQuote->number }}</p>
    </div>
    <a href="{{ route('price-quotes.show', $priceQuote) }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('price-quotes.update', $priceQuote) }}" method="POST">
    @csrf
    @method('PUT')
    @if($priceQuote->tender_id)
        <input type="hidden" name="tender_id" value="{{ $priceQuote->tender_id }}">
    @endif

    @include('tenders.price-quotes._form')

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ route('price-quotes.show', $priceQuote) }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
