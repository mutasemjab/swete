@extends('layouts.app')

@section('title', __('agreements.add_agreement'))
@section('breadcrumb', __('agreements.add_agreement'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('agreements.add_agreement') }}</h1>
    </div>
    <a href="{{ route('agreements.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('agreements.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @include('agreements._form', ['agreement' => null, 'customers' => $customers, 'suppliers' => $suppliers])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('agreements.add_agreement') }}
        </button>
        <a href="{{ route('agreements.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
