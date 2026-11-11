@extends('layouts.app')

@section('title', __('tenders.edit_tender'))
@section('breadcrumb', __('tenders.edit_tender'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('tenders.edit_tender') }}</h1>
        <p class="page-subtitle">{{ $tender->localized_title }}</p>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('tenders.update', $tender) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="back" value="{{ $backUrl }}">
    @include('tenders._form', ['tender' => $tender, 'customers' => $customers, 'statuses' => $statuses, 'countries' => $countries, 'governorates' => $governorates])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
