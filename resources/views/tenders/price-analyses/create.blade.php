@extends('layouts.app')

@section('title', __('tenders.add_price_analysis'))
@section('breadcrumb', __('tenders.add_price_analysis'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('tenders.add_price_analysis') }}</h1>
        <p class="page-subtitle">{{ __('tenders.add_price_analysis_subtitle') }}</p>
    </div>
    <a href="{{ route('price-analyses.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

@if($ciatDiscounts->isEmpty())
    <div class="card px-6 py-10 text-center">
        <p class="text-slate-800 font-bold text-lg mb-4">{{ __('tenders.no_ciat_discounts_available') }}</p>
        <a href="{{ route('ciat-discounts.create') }}" class="btn-primary">
            <i class="fa-solid fa-plus"></i>
            {{ __('tenders.add_ciat_discount') }}
        </a>
    </div>
@else
<form action="{{ route('price-analyses.store') }}" method="POST">
    @csrf
    @include('tenders.price-analyses._form', ['analysis' => null, 'branches' => $branches, 'ciatDiscounts' => $ciatDiscounts])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('tenders.add_price_analysis') }}
        </button>
        <a href="{{ route('price-analyses.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endif
@endsection
