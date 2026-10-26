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
                    {{ __('tenders.location_outside_jordan') }} — {{ $tender->country?->localized_name }}
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

@if($tender->extraFieldValues->isNotEmpty())
<div class="card px-6 py-5 mb-5">
    <h3 class="text-sm font-black text-slate-700 mb-3 flex items-center gap-2">
        <i class="fa-solid fa-sliders text-amber-500 text-sm"></i>
        {{ __('clients.extra_fields_section') }}
    </h3>
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        @foreach($tender->extraFieldValues as $efv)
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ $efv->field?->localized_label }}</dt>
            <dd class="font-bold text-slate-800">
                @if($efv->file_path)
                    <a href="{{ asset($efv->file_path) }}" target="_blank" class="block">
                        <img src="{{ asset($efv->file_path) }}" class="h-20 rounded-lg object-cover border border-slate-200">
                    </a>
                @else
                    {{ $efv->value ?: '—' }}
                @endif
            </dd>
        </div>
        @endforeach
    </dl>
</div>
@endif

<div class="card overflow-hidden mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700">{{ __('tenders.analysis_section') }}</h3>
        <a href="{{ route('price-analyses.create', ['tender_id' => $tender->id]) }}" class="btn-primary btn-sm">
            <i class="fa-solid fa-plus"></i>
            {{ __('tenders.new_analysis') }}
        </a>
    </div>
    @if($tender->analyses->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.analysis_number') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.analysis_branch') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.created_at') }}</th>
                        <th class="px-5 py-3 w-20"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($tender->analyses as $analysis)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-3 font-mono font-bold text-indigo-600">
                            <a href="{{ route('price-analyses.show', $analysis) }}" class="hover:underline">{{ $analysis->number }}</a>
                        </td>
                        <td class="px-5 py-3 text-slate-600">{{ $analysis->branch?->localized_name }}</td>
                        <td class="px-5 py-3 text-slate-600" dir="ltr">{{ $analysis->created_at->format('Y-m-d') }}</td>
                        <td class="px-5 py-3 text-end">
                            <a href="{{ route('price-analyses.show', $analysis) }}" class="btn-secondary btn-sm">
                                <i class="fa-solid fa-eye"></i> {{ __('app.view') }}
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="px-6 py-6 text-sm text-slate-400">{{ __('tenders.no_analyses') }}</p>
    @endif
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
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('accounting.customer') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.quote_date') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.quote_total') }}</th>
                        <th class="px-5 py-3 w-48"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php
                        // Whenever a project for this tender is still awaiting its first approval,
                        // converting ANY quote (a later revision of the one already linked, a
                        // different customer, whatever) is never silently auto-decided — the user
                        // explicitly picks "add as Variation Order" or "replace entirely" instead.
                        // An ACTIVE (already-approved) project keeps the old, unprompted behavior.
                        $underReviewProject = $tender->projects->first(fn ($p) => $p->is_current && $p->status === 'under_review');
                    @endphp
                    @foreach($tender->priceQuotes as $quote)
                    <tr class="hover:bg-slate-50/50 transition-colors" @if($underReviewProject && ! $quote->isLocked() && ! $quote->hasPendingVariationOrder()) x-data="{ showConversionModal: false }" @endif>
                        <td class="px-5 py-3">
                            <a href="{{ route('price-quotes.show', $quote) }}" class="font-mono font-bold text-indigo-600 hover:underline">{{ $quote->number }}</a>
                            @unless($quote->is_current)
                                <span class="badge bg-slate-100 text-slate-500">{{ __('tenders.quote_old_version_badge') }}</span>
                            @endunless
                        </td>
                        <td class="px-5 py-3 text-slate-700">{{ $quote->customer?->localized_name ?? '—' }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $quote->date->format('Y-m-d') }}</td>
                        <td class="px-5 py-3 font-bold text-slate-800">{{ number_format($quote->total, 3) }}</td>
                        <td class="px-5 py-3 text-end">
                            @if($quote->isLocked())
                                <span class="badge bg-emerald-100 text-emerald-700">{{ __('tenders.quote_converted_badge') }}</span>
                            @elseif($quote->hasPendingVariationOrder())
                                <span class="badge bg-amber-100 text-amber-700">{{ __('tenders.quote_pending_vo_badge') }}</span>
                            @elseif($underReviewProject)
                                <button type="button" @click="showConversionModal = true" class="btn-primary btn-sm">
                                    <i class="fa-solid fa-diagram-project"></i>
                                    {{ __('tenders.convert_to_project') }}
                                </button>
                                <div x-show="showConversionModal" x-cloak class="fixed inset-0 z-[9998] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
                                    <div @click.outside="showConversionModal = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
                                        <div class="px-6 py-5 border-b border-slate-100">
                                            <h3 class="font-bold text-slate-800">{{ __('tenders.quote_conversion_modal_title') }}</h3>
                                            <p class="text-sm text-slate-500 mt-1">{{ __('tenders.quote_conversion_modal_body') }}</p>
                                        </div>
                                        <div class="px-6 py-5 space-y-3">
                                            <form action="{{ route('price-quotes.convert-to-project', $quote) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="mode" value="add">
                                                <button type="submit" class="btn-secondary w-full justify-start text-start">
                                                    <i class="fa-solid fa-plus"></i>
                                                    <span>
                                                        <span class="block font-bold">{{ __('tenders.quote_conversion_add_vo') }}</span>
                                                        <span class="block text-xs text-slate-400 font-normal mt-0.5">{{ __('tenders.quote_conversion_add_vo_hint') }}</span>
                                                    </span>
                                                </button>
                                            </form>
                                            <form action="{{ route('price-quotes.convert-to-project', $quote) }}" method="POST"
                                                  @submit="if (! confirm('{{ __('tenders.convert_to_project_confirm') }}')) $event.preventDefault()">
                                                @csrf
                                                <input type="hidden" name="mode" value="replace">
                                                <button type="submit" class="btn-danger w-full justify-start text-start">
                                                    <i class="fa-solid fa-right-left"></i>
                                                    <span>
                                                        <span class="block font-bold">{{ __('tenders.quote_conversion_replace') }}</span>
                                                        <span class="block text-xs text-rose-100 font-normal mt-0.5">{{ __('tenders.quote_conversion_replace_hint') }}</span>
                                                    </span>
                                                </button>
                                            </form>
                                        </div>
                                        <div class="px-6 py-4 border-t border-slate-100 flex justify-end">
                                            <button type="button" @click="showConversionModal = false" class="btn-secondary">{{ __('app.cancel') }}</button>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <form action="{{ route('price-quotes.convert-to-project', $quote) }}" method="POST"
                                      @submit="if (! confirm('{{ __('tenders.convert_to_project_confirm') }}')) $event.preventDefault()">
                                    @csrf
                                    <button type="submit" class="btn-primary btn-sm">
                                        <i class="fa-solid fa-diagram-project"></i>
                                        {{ __('tenders.convert_to_project') }}
                                    </button>
                                </form>
                            @endif
                        </td>
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

@if($tender->projects->isNotEmpty())
<div class="card overflow-hidden mt-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700">{{ __('tenders.projects') }}</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.project_number') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.project_title') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.project_status') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($tender->projects as $project)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-5 py-3">
                        <a href="{{ route('projects.show', $project) }}" class="font-mono font-bold text-indigo-600 hover:underline">{{ $project->number }}</a>
                    </td>
                    <td class="px-5 py-3 font-bold text-slate-800">{{ $project->localized_title }}</td>
                    <td class="px-5 py-3">
                        <span class="badge bg-emerald-100 text-emerald-700">{{ __('tenders.project_status_' . $project->status) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
