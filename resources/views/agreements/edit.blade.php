@extends('layouts.app')

@section('title', __('agreements.edit_agreement'))
@section('breadcrumb', __('agreements.edit_agreement'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('agreements.edit_agreement') }}</h1>
    </div>
    <a href="{{ route('agreements.show', $agreement) }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('agreements.update', $agreement) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    @include('agreements._form', ['agreement' => $agreement, 'customers' => $customers, 'suppliers' => $suppliers])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ route('agreements.show', $agreement) }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
