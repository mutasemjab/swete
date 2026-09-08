@extends('layouts.app')

@section('title', __('external_purchases.shipping_companies_list'))
@section('breadcrumb', __('external_purchases.shipping_companies'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('external_purchases.shipping_companies_list') }}</h1>
        <p class="page-subtitle">{{ __('external_purchases.shipping_companies_subtitle') }}</p>
    </div>
    <a href="{{ route('shipping-companies.create') }}" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        {{ __('external_purchases.add_shipping_company') }}
    </a>
</div>

<form method="GET" action="{{ route('shipping-companies.index') }}" class="card px-5 py-4 mb-4 flex flex-wrap gap-3">
    <div class="flex-1 min-w-48">
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute start-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="{{ __('app.search') }}..."
                   class="form-input ps-10">
        </div>
    </div>
    <button type="submit" class="btn-primary">
        <i class="fa-solid fa-filter"></i>
        {{ __('app.search') }}
    </button>
    @if(request()->hasAny(['search']))
        <a href="{{ route('shipping-companies.index') }}" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            {{ __('app.clear_filters') }}
        </a>
    @endif
</form>

<div class="card overflow-hidden">
    @if($shippingCompanies->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-ship text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('external_purchases.no_shipping_companies') }}</p>
            <a href="{{ route('shipping-companies.create') }}" class="btn-primary mt-5">
                <i class="fa-solid fa-plus"></i>
                {{ __('external_purchases.add_shipping_company') }}
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.name') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.email') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden md:table-cell">{{ __('app.country') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('external_purchases.shipping_company_rating') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.status') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($shippingCompanies as $company)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-4"><p class="font-bold text-slate-800">{{ $company->localized_name }}</p></td>
                        <td class="px-5 py-4 text-sm text-slate-600" dir="ltr">{{ $company->email }}</td>
                        <td class="px-5 py-4 hidden md:table-cell text-sm text-slate-600">{{ $company->country?->localized_name ?? '—' }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $company->rating ?? '—' }}</td>
                        <td class="px-5 py-4">
                            @if($company->status)
                                <span class="badge bg-emerald-100 text-emerald-700">{{ __('app.active') }}</span>
                            @else
                                <span class="badge bg-slate-100 text-slate-500">{{ __('app.inactive') }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('shipping-companies.edit', $company) }}"
                                   class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="{{ __('app.edit') }}">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                <button type="button" title="{{ __('app.delete') }}"
                                        @click="$dispatch('delete-confirm', { action: '{{ route('shipping-companies.destroy', $company) }}', message: '{{ __('app.delete_confirm_msg') }}' })"
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

        @if($shippingCompanies->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                {{ __('app.showing') }} <span class="font-bold text-slate-700">{{ $shippingCompanies->firstItem() }}</span>
                {{ __('app.to') }} <span class="font-bold text-slate-700">{{ $shippingCompanies->lastItem() }}</span>
                {{ __('app.of') }} <span class="font-bold text-slate-700">{{ $shippingCompanies->total() }}</span>
                {{ __('app.results') }}
            </p>
            <div class="flex gap-1">
                @if($shippingCompanies->onFirstPage())
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.previous') }}</span>
                @else
                    <a href="{{ $shippingCompanies->previousPageUrl() }}" class="btn-secondary btn-sm">{{ __('app.previous') }}</a>
                @endif
                @if($shippingCompanies->hasMorePages())
                    <a href="{{ $shippingCompanies->nextPageUrl() }}" class="btn-primary btn-sm">{{ __('app.next') }}</a>
                @else
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.next') }}</span>
                @endif
            </div>
        </div>
        @endif
    @endif
</div>
@endsection
