@extends('layouts.app')

@section('title', __('tenders.edit_ciat_discount'))
@section('breadcrumb', __('tenders.edit_ciat_discount'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('tenders.edit_ciat_discount') }}</h1>
        <p class="page-subtitle">{{ $discount->label }}</p>
    </div>
    <a href="{{ route('ciat-discounts.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('ciat-discounts.update', $discount) }}" method="POST">
    @csrf
    @method('PUT')
    @include('tenders.ciat-discounts._form')

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ route('ciat-discounts.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
