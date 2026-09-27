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
        <option value="">{{ __('accounting.customer') }}</option>
        @foreach($customers as $customer)
            <option value="{{ $customer->id }}" @selected(request('customer_id') == $customer->id)>{{ $customer->localized_name }}</option>
        @endforeach
    </select>
    <select name="assigned_to" class="js-select2 form-select w-56">
        <option value="">{{ __('tenders.all_employees') }}</option>
        @foreach($employees as $employee)
            <option value="{{ $employee->id }}" @selected(request('assigned_to') == $employee->id)>{{ $employee->name }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn-primary">
        <i class="fa-solid fa-filter"></i>
        {{ __('app.search') }}
    </button>
    @if(request()->hasAny(['customer_id', 'assigned_to']))
        <a href="{{ route('price-quotes.index') }}" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            {{ __('app.clear_filters') }}
        </a>
    @endif
</form>

<div class="card overflow-hidden" x-data="{
        selected: [],
        showAssignModal: false,
        toggleAll(checked) { this.selected = checked ? {{ $priceQuotes->getCollection()->whereNull('invoice_id')->pluck('id')->values()->toJson() }} : []; },
      }">
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
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between" x-show="selected.length > 0" x-cloak>
            <p class="text-sm font-semibold text-slate-600" x-text="selected.length + ' {{ __('app.selected') }}'"></p>
            <button type="button" @click="showAssignModal = true" class="btn-secondary btn-sm">
                <i class="fa-solid fa-paper-plane"></i>
                {{ __('tenders.quote_send_to_employee') }}
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 w-10">
                            <input type="checkbox" @change="toggleAll($event.target.checked)" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        </th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.quote_number') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('accounting.customer') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden md:table-cell">{{ __('tenders.quote_tender') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.quote_date') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.quote_total') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.quote_assigned_to') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($priceQuotes as $quote)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-4">
                            @if(! $quote->invoice_id)
                                <input type="checkbox" value="{{ $quote->id }}" x-model.number="selected" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            @endif
                        </td>
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
                        <td class="px-5 py-4">
                            @if($quote->invoice_id)
                                <span class="badge bg-emerald-100 text-emerald-700">{{ __('tenders.quote_status_invoiced') }}</span>
                            @elseif($quote->assignee)
                                <span class="badge bg-violet-100 text-violet-700">{{ $quote->assignee->name }}</span>
                            @else
                                <span class="text-xs text-slate-400">{{ __('tenders.quote_unassigned') }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5">
                                @if($quote->invoice_id)
                                    <a href="{{ route('accounting.invoices.show', $quote->invoice_id) }}"
                                       class="p-1.5 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all" title="{{ __('tenders.quote_view_invoice') }}">
                                        <i class="fa-solid fa-file-invoice-dollar text-sm"></i>
                                    </a>
                                @elseif($quote->assigned_to === Auth::id())
                                    <form action="{{ route('price-quotes.convert-to-invoice', $quote) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn-secondary btn-sm" title="{{ __('tenders.quote_convert_to_invoice') }}">
                                            <i class="fa-solid fa-file-invoice-dollar"></i>
                                            {{ __('tenders.quote_convert_to_invoice') }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
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

        {{-- Send-to-employee modal --}}
        <div x-show="showAssignModal" x-cloak
             class="fixed inset-0 z-[9998] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
            <div @click.outside="showAssignModal = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
                <form action="{{ route('price-quotes.assign') }}" method="POST">
                    @csrf
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="quote_ids[]" :value="id">
                    </template>
                    <div class="px-6 py-5 border-b border-slate-100">
                        <h3 class="font-bold text-slate-800">{{ __('tenders.quote_send_to_employee') }}</h3>
                        <p class="text-xs text-slate-400 mt-1">{{ __('tenders.quote_send_to_employee_hint') }}</p>
                    </div>
                    <div class="px-6 py-5">
                        <label class="form-label">{{ __('tenders.quote_select_employee') }} <span class="text-rose-500">*</span></label>
                        <select name="assigned_to" class="form-select" required>
                            <option value="">{{ __('app.select') }}</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="showAssignModal = false" class="btn-secondary">{{ __('app.cancel') }}</button>
                        <button type="submit" class="btn-primary">
                            <i class="fa-solid fa-paper-plane"></i>
                            {{ __('tenders.quote_send') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
