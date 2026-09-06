@extends('layouts.app')

@section('title', __('accounting.add_supplier'))
@section('breadcrumb', __('accounting.add_supplier'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('accounting.add_supplier') }}</h1>
    </div>
    <a href="{{ route('accounting.suppliers.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('accounting.suppliers.store') }}" method="POST">
    @csrf
    @include('accounting.suppliers._form', ['groups' => $groups])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('accounting.add_supplier') }}
        </button>
        <a href="{{ route('accounting.suppliers.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
