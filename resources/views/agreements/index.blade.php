@extends('layouts.app')

@section('title', __('agreements.agreements_list'))
@section('breadcrumb', __('agreements.agreements_list'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('agreements.agreements_list') }}</h1>
        <p class="page-subtitle">{{ __('agreements.agreements_subtitle') }}</p>
    </div>
    <a href="{{ route('agreements.create') }}" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        {{ __('agreements.add_agreement') }}
    </a>
</div>

<form method="GET" action="{{ route('agreements.index') }}" class="card px-5 py-4 mb-4 flex flex-wrap gap-3">
    <select name="party_type" class="form-select w-56">
        <option value="">{{ __('app.all') }}</option>
        <option value="customer" @selected(request('party_type') === 'customer')>{{ __('agreements.party_type_customer') }}</option>
        <option value="supplier" @selected(request('party_type') === 'supplier')>{{ __('agreements.party_type_supplier') }}</option>
    </select>
    <button type="submit" class="btn-primary">
        <i class="fa-solid fa-filter"></i>
        {{ __('app.search') }}
    </button>
    @if(request()->hasAny(['party_type']))
        <a href="{{ route('agreements.index') }}" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            {{ __('app.clear_filters') }}
        </a>
    @endif
</form>

<div class="card overflow-hidden">
    @if($agreements->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-file-signature text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('agreements.no_agreements') }}</p>
            <a href="{{ route('agreements.create') }}" class="btn-primary mt-5">
                <i class="fa-solid fa-plus"></i>
                {{ __('agreements.add_agreement') }}
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('agreements.agreement_number') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('agreements.party_type') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('agreements.party') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.created_at') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($agreements as $agreement)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-4">
                            <a href="{{ route('agreements.show', $agreement) }}" class="font-mono font-bold text-slate-700 hover:text-indigo-600 transition-colors">{{ $agreement->number }}</a>
                        </td>
                        <td class="px-5 py-4">
                            @if($agreement->party_type === 'customer')
                                <span class="badge bg-violet-100 text-violet-700">{{ __('agreements.party_type_customer') }}</span>
                            @else
                                <span class="badge bg-orange-100 text-orange-700">{{ __('agreements.party_type_supplier') }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $agreement->party?->localized_name }}</td>
                        <td class="px-5 py-4 text-sm text-slate-500">{{ $agreement->created_at->format('Y-m-d') }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('agreements.show', $agreement) }}"
                                   class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="{{ __('app.view') }}">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('agreements.edit', $agreement) }}"
                                   class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="{{ __('app.edit') }}">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                <button type="button" title="{{ __('app.delete') }}"
                                        @click="$dispatch('delete-confirm', { action: '{{ route('agreements.destroy', $agreement) }}', message: '{{ __('app.delete_confirm_msg') }}' })"
                                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($agreements->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                {{ __('app.showing') }} <span class="font-bold text-slate-700">{{ $agreements->firstItem() }}</span>
                {{ __('app.to') }} <span class="font-bold text-slate-700">{{ $agreements->lastItem() }}</span>
                {{ __('app.of') }} <span class="font-bold text-slate-700">{{ $agreements->total() }}</span>
                {{ __('app.results') }}
            </p>
            <div class="flex gap-1">
                @if($agreements->onFirstPage())
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.previous') }}</span>
                @else
                    <a href="{{ $agreements->previousPageUrl() }}" class="btn-secondary btn-sm">{{ __('app.previous') }}</a>
                @endif
                @if($agreements->hasMorePages())
                    <a href="{{ $agreements->nextPageUrl() }}" class="btn-primary btn-sm">{{ __('app.next') }}</a>
                @else
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.next') }}</span>
                @endif
            </div>
        </div>
        @endif
    @endif
</div>
@endsection
