@extends('layouts.app')

@section('title', $tender->number)
@section('breadcrumb', $tender->number)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title flex items-center gap-3">
            {{ $tender->number }}
            <span class="badge bg-{{ $tender->statusRef?->color ?? 'slate' }}-100 text-{{ $tender->statusRef?->color ?? 'slate' }}-700">{{ $tender->statusRef?->localized_name }}</span>
        </h1>
        <p class="page-subtitle">{{ $tender->localized_title }}</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('tenders.edit', $tender) }}" class="btn-secondary">
            <i class="fa-solid fa-pen"></i>
            {{ __('app.edit') }}
        </a>
        <a href="{{ route('tenders.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
            {{ __('app.back_to_list') }}
        </a>
    </div>
</div>

<div class="card px-6 py-5 mb-5">
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.tender_entity_name') }}</dt>
            <dd class="font-bold text-slate-800">{{ $tender->localized_entity_name }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.tender_customer') }}</dt>
            <dd class="font-bold text-slate-800">{{ $tender->party?->localized_name ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.tender_submission_deadline') }}</dt>
            <dd class="font-bold text-slate-800">{{ $tender->submission_deadline->format('Y-m-d') }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.tender_location_scope') }}</dt>
            <dd class="font-bold text-slate-800">
                @if($tender->location_scope === 'outside_jordan')
                    {{ __('tenders.location_outside_jordan') }} — {{ $tender->country }}
                @else
                    {{ __('tenders.location_inside_jordan') }} — {{ $tender->localized_governorate }}
                @endif
            </dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.tender_delivery_terms') }}</dt>
            <dd class="font-bold text-slate-800">{{ $tender->delivery_terms ? __('tenders.delivery_terms_' . $tender->delivery_terms) : '—' }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.tender_coverage') }}</dt>
            <dd class="font-bold text-slate-800">{{ $tender->coverage ? __('tenders.coverage_' . $tender->coverage) : '—' }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.tender_tax_exempt') }}</dt>
            <dd class="font-bold text-slate-800">{{ $tender->tax_exempt ? __('app.yes') : __('app.no') }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.tender_customs_exempt') }}</dt>
            <dd class="font-bold text-slate-800">{{ $tender->customs_exempt ? __('app.yes') : __('app.no') }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.tender_win_probability') }}</dt>
            <dd class="font-bold text-slate-800">{{ $tender->win_probability !== null ? $tender->win_probability . '%' : '—' }}</dd>
        </div>
        @if($tender->description)
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.tender_description') }}</dt>
            <dd class="text-slate-700">{{ $tender->description }}</dd>
        </div>
        @endif
        @if($tender->notes)
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.tender_notes') }}</dt>
            <dd class="text-slate-700">{{ $tender->notes }}</dd>
        </div>
        @endif
    </dl>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
    <div class="card px-6 py-5">
        <h3 class="text-sm font-black text-slate-700 mb-2">{{ __('tenders.tender_documents_url') }}</h3>
        @if($tender->documents_url)
            <a href="{{ $tender->documents_url }}" target="_blank" rel="noopener" class="text-indigo-600 hover:underline break-all text-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>{{ $tender->documents_url }}
            </a>
        @else
            <p class="text-sm text-slate-400">—</p>
        @endif
    </div>
    <div class="card px-6 py-5">
        <h3 class="text-sm font-black text-slate-700 mb-2">{{ __('tenders.tender_design_documents_url') }}</h3>
        @if($tender->design_documents_url)
            <a href="{{ $tender->design_documents_url }}" target="_blank" rel="noopener" class="text-indigo-600 hover:underline break-all text-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>{{ $tender->design_documents_url }}
            </a>
        @else
            <p class="text-sm text-slate-400">—</p>
        @endif
    </div>
</div>

<div class="card overflow-hidden">
    <div class="card-header">
        <h3 class="font-bold text-slate-700">{{ __('tenders.price_quotes') }}</h3>
        <a href="{{ route('price-quotes.create', ['tender_id' => $tender->id]) }}" class="btn-primary btn-sm">
            <i class="fa-solid fa-plus"></i>
            {{ __('tenders.add_quote') }}
        </a>
    </div>

    @if($tender->priceQuotes->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.quote_number') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.quote_date') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.quote_total') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($tender->priceQuotes as $quote)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-3">
                            <a href="{{ route('price-quotes.show', $quote) }}" class="font-mono font-bold text-indigo-600 hover:underline">{{ $quote->number }}</a>
                        </td>
                        <td class="px-5 py-3 text-slate-600">{{ $quote->date->format('Y-m-d') }}</td>
                        <td class="px-5 py-3 font-bold text-slate-800">{{ number_format($quote->total, 3) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="px-6 py-6 text-sm text-slate-400">{{ __('tenders.no_quotes') }}</p>
    @endif

    @if($unlinkedQuotes->isNotEmpty())
    <form action="{{ route('tenders.attach-quote', $tender) }}" method="POST" class="px-6 py-4 border-t border-slate-100 flex items-end gap-3 flex-wrap">
        @csrf
        <div class="min-w-56">
            <label class="form-label">{{ __('tenders.attach_existing_quote') }}</label>
            <select name="price_quote_id" class="js-select2 form-select" required>
                <option value="">{{ __('app.select') }}</option>
                @foreach($unlinkedQuotes as $quote)
                    <option value="{{ $quote->id }}">{{ $quote->number }} — {{ $quote->customer?->localized_name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-secondary">
            <i class="fa-solid fa-link"></i>
            {{ __('tenders.attach_quote') }}
        </button>
    </form>
    @endif
</div>
@endsection
