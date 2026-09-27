@extends('layouts.app')

@section('title', __('tenders.add_ciat_discount'))
@section('breadcrumb', __('tenders.add_ciat_discount'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('tenders.add_ciat_discount') }}</h1>
    </div>
    <a href="{{ route('ciat-discounts.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('ciat-discounts.store') }}" method="POST">
    @csrf
    @include('tenders.ciat-discounts._form', ['discount' => null])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('tenders.add_ciat_discount') }}
        </button>
        <a href="{{ route('ciat-discounts.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
