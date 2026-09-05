@extends('layouts.app')

@section('title', __('warehouse.add_unit'))
@section('breadcrumb', __('warehouse.add_unit'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('warehouse.add_unit') }}</h1>
        <p class="page-subtitle">{{ __('warehouse.add_unit_subtitle') }}</p>
    </div>
    <a href="{{ route('warehouse.units.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('warehouse.units.store') }}" method="POST">
    @csrf
    @include('warehouse.units._form')

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('warehouse.add_unit') }}
        </button>
        <a href="{{ route('warehouse.units.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
