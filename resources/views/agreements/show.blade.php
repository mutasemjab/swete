@extends('layouts.app')

@section('title', $agreement->number)
@section('breadcrumb', $agreement->number)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $agreement->number }}</h1>
        <p class="page-subtitle">{{ $agreement->party?->localized_name }}</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ $agreement->url }}" target="_blank" rel="noopener" class="btn-secondary">
            <i class="fa-solid fa-file-pdf"></i>
            {{ __('agreements.view_current_file') }}
        </a>
        <a href="{{ route('agreements.edit', $agreement) }}" class="btn-secondary">
            <i class="fa-solid fa-pen"></i>
            {{ __('app.edit') }}
        </a>
        <a href="{{ route('agreements.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
            {{ __('app.back_to_list') }}
        </a>
    </div>
</div>

<div class="card px-6 py-5 mb-5">
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('agreements.party_type') }}</dt>
            <dd class="font-bold text-slate-800">
                @if($agreement->party_type === 'customer')
                    <span class="badge bg-violet-100 text-violet-700">{{ __('agreements.party_type_customer') }}</span>
                @else
                    <span class="badge bg-orange-100 text-orange-700">{{ __('agreements.party_type_supplier') }}</span>
                @endif
            </dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('agreements.party') }}</dt>
            <dd class="font-bold text-slate-800">{{ $agreement->party?->localized_name }} ({{ $agreement->party?->code }})</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('app.created_at') }}</dt>
            <dd class="font-bold text-slate-800">{{ $agreement->created_at->format('Y-m-d H:i') }} — {{ $agreement->creator?->name }}</dd>
        </div>
        @if($agreement->notes)
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('app.notes') }}</dt>
            <dd class="text-slate-700">{{ $agreement->notes }}</dd>
        </div>
        @endif
    </dl>
</div>
@endsection
