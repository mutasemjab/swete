@extends('layouts.app')

@section('title', __('warehouse.add_category'))
@section('breadcrumb', __('warehouse.add_category'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('warehouse.add_category') }}</h1>
        <p class="page-subtitle">{{ __('warehouse.add_category_subtitle') }}</p>
    </div>
    <a href="{{ route('warehouse.categories.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('warehouse.categories.store') }}" method="POST">
    @csrf
    @include('warehouse.categories._form', ['parents' => $parents])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('warehouse.add_category') }}
        </button>
        <a href="{{ route('warehouse.categories.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
