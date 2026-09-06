@extends('layouts.app')

@section('title', __('tenders.price_quotes_list'))
@section('breadcrumb', __('tenders.price_quotes'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('tenders.price_quotes_list') }}</h1>
        <p class="page-subtitle">{{ __('tenders.price_quotes_subtitle') }}</p>
    </div>
    <a href="{{ route('price-quotes.create') }}" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        {{ __('tenders.add_quote') }}
    </a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="card px-5 py-4 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-orange-100 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-file-invoice text-orange-600"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800">{{ $priceQuotes->total() }}</p>
            <p class="text-xs text-slate-500 font-medium mt-0.5">{{ __('tenders.total_quotes') }}</p>
        </div>
    </div>
</div>

<form method="GET" action="{{ route('price-quotes.index') }}" class="card px-5 py-4 mb-4 flex flex-wrap gap-3">
    <select name="customer_id" class="js-select2 form-select w-56">
        <option value="">{{ __('app.select') }}</option>
        @foreach($customers as $customer)
            <option value="{{ $customer->id }}" @selected(request('customer_id') == $customer->id)>{{ $customer->localized_name }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn-primary">
        <i class="fa-solid fa-filter"></i>
        {{ __('app.search') }}
    </button>
    @if(request()->hasAny(['customer_id']))
        <a href="{{ route('price-quotes.index') }}" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            {{ __('app.clear_filters') }}
        </a>
    @endif
</form>

<div class="card overflow-hidden">
    @if($priceQuotes->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-file-invoice text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('tenders.no_quotes') }}</p>
            <a href="{{ route('price-quotes.create') }}" class="btn-primary mt-5">
                <i class="fa-solid fa-plus"></i>
                {{ __('tenders.add_quote') }}
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.quote_number') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('accounting.customer') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden md:table-cell">{{ __('tenders.quote_tender') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.quote_date') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.quote_total') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($priceQuotes as $quote)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <a href="{{ route('price-quotes.show', $quote) }}" class="font-mono font-bold text-slate-700 hover:text-indigo-600 transition-colors">{{ $quote->number }}</a>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $quote->customer?->localized_name }}</td>
                        <td class="px-5 py-4 hidden md:table-cell text-sm text-slate-600">
                            @if($quote->tender)
                                <a href="{{ route('tenders.show', $quote->tender) }}" class="hover:underline">{{ $quote->tender->number }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $quote->date->format('Y-m-d') }}</td>
                        <td class="px-5 py-4 font-bold text-slate-800">{{ number_format($quote->total, 3) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($priceQuotes->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                {{ __('app.showing') }} <span class="font-bold text-slate-700">{{ $priceQuotes->firstItem() }}</span>
                {{ __('app.to') }} <span class="font-bold text-slate-700">{{ $priceQuotes->lastItem() }}</span>
                {{ __('app.of') }} <span class="font-bold text-slate-700">{{ $priceQuotes->total() }}</span>
                {{ __('app.results') }}
            </p>
            <div class="flex gap-1">
                @if($priceQuotes->onFirstPage())
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.previous') }}</span>
                @else
                    <a href="{{ $priceQuotes->previousPageUrl() }}" class="btn-secondary btn-sm">{{ __('app.previous') }}</a>
                @endif
                @if($priceQuotes->hasMorePages())
                    <a href="{{ $priceQuotes->nextPageUrl() }}" class="btn-primary btn-sm">{{ __('app.next') }}</a>
                @else
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.next') }}</span>
                @endif
            </div>
        </div>
        @endif
    @endif
</div>
@endsection
