@extends('layouts.app')

@section('title', __('settings.add_sales_rep'))
@section('breadcrumb', __('settings.add_sales_rep'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('settings.add_sales_rep') }}</h1>
    </div>
    <a href="{{ route('settings.sales-representatives.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('settings.sales-representatives.store') }}" method="POST">
    @csrf
    @include('settings.sales-representatives._form')

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('settings.add_sales_rep') }}
        </button>
        <a href="{{ route('settings.sales-representatives.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
