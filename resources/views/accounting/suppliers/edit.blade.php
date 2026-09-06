@extends('layouts.app')

@section('title', __('accounting.edit_supplier'))
@section('breadcrumb', __('accounting.edit_supplier'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('accounting.edit_supplier') }}</h1>
        <p class="page-subtitle">{{ $supplier->localized_name }}</p>
    </div>
    <a href="{{ route('accounting.suppliers.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('accounting.suppliers.update', $supplier) }}" method="POST">
    @csrf
    @method('PUT')
    @include('accounting.suppliers._form', ['groups' => $groups, 'supplier' => $supplier])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ route('accounting.suppliers.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
