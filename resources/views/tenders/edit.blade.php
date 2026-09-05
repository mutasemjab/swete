@extends('layouts.app')

@section('title', __('tenders.edit_tender'))
@section('breadcrumb', __('tenders.edit_tender'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('tenders.edit_tender') }}</h1>
        <p class="page-subtitle">{{ $tender->localized_title }}</p>
    </div>
    <a href="{{ route('tenders.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('tenders.update', $tender) }}" method="POST">
    @csrf
    @method('PUT')
    @include('tenders._form', ['tender' => $tender])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ route('tenders.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
