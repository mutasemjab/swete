@extends('layouts.app')

@section('title', __('accounting.edit_group'))
@section('breadcrumb', __('accounting.edit_group'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('accounting.edit_group') }}</h1>
        <p class="page-subtitle">{{ $group->localized_name }}</p>
    </div>
    <a href="{{ route('accounting.supplier-groups.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('accounting.supplier-groups.update', $group) }}" method="POST">
    @csrf
    @method('PUT')
    @include('accounting.supplier-groups._form', ['parents' => $parents, 'group' => $group])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ route('accounting.supplier-groups.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
