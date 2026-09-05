@extends('layouts.app')

@section('title', __('accounting.edit_' . $type))
@section('breadcrumb', __('accounting.edit_' . $type))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('accounting.edit_' . $type) }}</h1>
        <p class="page-subtitle">{{ $party->localized_name }}</p>
    </div>
    <a href="{{ route("accounting.{$type}s.index") }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route("accounting.{$type}s.update", $party) }}" method="POST">
    @csrf
    @method('PUT')
    @include('accounting.parties._form', ['groups' => $groups, 'type' => $type, 'party' => $party])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ route("accounting.{$type}s.index") }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
