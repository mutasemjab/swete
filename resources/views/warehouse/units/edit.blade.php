@extends('layouts.app')

@section('title', __('warehouse.edit_unit'))
@section('breadcrumb', __('warehouse.edit_unit'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('warehouse.edit_unit') }}</h1>
        <p class="page-subtitle">{{ $unit->localized_name }}</p>
    </div>
    <a href="{{ route('warehouse.units.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('warehouse.units.update', $unit) }}" method="POST">
    @csrf
    @method('PUT')
    @include('warehouse.units._form', ['unit' => $unit])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ route('warehouse.units.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
