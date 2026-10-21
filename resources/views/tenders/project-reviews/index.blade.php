@extends('layouts.app')

@section('title', __('tenders.projects_under_review_list'))
@section('breadcrumb', __('tenders.projects_under_review'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('tenders.projects_under_review_list') }}</h1>
        <p class="page-subtitle">{{ __('tenders.projects_under_review_subtitle') }}</p>
    </div>
</div>

<form method="GET" action="{{ route('project-reviews.index') }}" class="card px-5 py-4 mb-4 flex flex-wrap gap-3">
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
        <a href="{{ route('project-reviews.index') }}" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            {{ __('app.clear_filters') }}
        </a>
    @endif
</form>

<div class="card overflow-hidden">
    @if($projects->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-hourglass-half text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('tenders.no_projects_under_review') }}</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.project_number') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.project_title') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden lg:table-cell">{{ __('tenders.project_customer') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden md:table-cell">{{ __('tenders.project_tender') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($projects as $project)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <a href="{{ route('projects.show', $project) }}" class="font-mono font-bold text-slate-700 hover:text-indigo-600 transition-colors bg-slate-100 px-2 py-1 rounded-lg text-sm">{{ $project->number }}</a>
                            <span class="badge bg-purple-100 text-purple-700">P{{ $project->variation_round }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <p class="font-bold text-slate-800">{{ $project->localized_title }}</p>
                            @if($project->review_reason)
                                <p class="text-xs text-amber-600 mt-1"><i class="fa-solid fa-triangle-exclamation"></i> {{ $project->review_reason }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4 hidden lg:table-cell text-sm text-slate-600">{{ $project->customer?->localized_name ?? '—' }}</td>
                        <td class="px-5 py-4 hidden md:table-cell text-sm text-slate-600">
                            @if($project->tender)
                                <a href="{{ route('tenders.show', $project->tender) }}" class="text-indigo-600 hover:underline">{{ $project->tender->number }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('projects.show', $project) }}" class="btn-secondary btn-sm">
                                    <i class="fa-solid fa-eye"></i> {{ __('app.view') }}
                                </a>
                                <form action="{{ route('project-reviews.approve', $project) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-primary btn-sm">
                                        <i class="fa-solid fa-check"></i> {{ __('tenders.project_approve') }}
                                    </button>
                                </form>
                                <form action="{{ route('project-reviews.reject', $project) }}" method="POST"
                                      @submit="if (! confirm('{{ __('tenders.project_reject_confirm') }}')) $event.preventDefault()">
                                    @csrf
                                    <button type="submit" class="btn-danger btn-sm">
                                        <i class="fa-solid fa-xmark"></i> {{ __('tenders.project_reject') }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($projects->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                {{ __('app.showing') }} <span class="font-bold text-slate-700">{{ $projects->firstItem() }}</span>
                {{ __('app.to') }} <span class="font-bold text-slate-700">{{ $projects->lastItem() }}</span>
                {{ __('app.of') }} <span class="font-bold text-slate-700">{{ $projects->total() }}</span>
                {{ __('app.results') }}
            </p>
            <div class="flex gap-1">
                @if($projects->onFirstPage())
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.previous') }}</span>
                @else
                    <a href="{{ $projects->previousPageUrl() }}" class="btn-secondary btn-sm">{{ __('app.previous') }}</a>
                @endif
                @if($projects->hasMorePages())
                    <a href="{{ $projects->nextPageUrl() }}" class="btn-primary btn-sm">{{ __('app.next') }}</a>
                @else
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.next') }}</span>
                @endif
            </div>
        </div>
        @endif
    @endif
</div>

@if($variationOrders->isNotEmpty())
<div class="card overflow-hidden mt-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-file-circle-plus text-purple-500 text-sm"></i>
            {{ __('tenders.variation_orders_section') }}
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.project_number') }}</th>
                    <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.quote_number') }}</th>
                    <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden md:table-cell">{{ __('tenders.project_tender') }}</th>
                    <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($variationOrders as $vo)
                <tr class="hover:bg-slate-50/50 transition-colors bg-purple-50/30">
                    <td class="px-5 py-4">
                        <a href="{{ route('projects.show', $vo->project) }}" class="font-mono font-bold text-slate-700 hover:text-indigo-600 transition-colors bg-slate-100 px-2 py-1 rounded-lg text-sm">{{ $vo->project->number }}</a>
                        <span class="badge bg-purple-100 text-purple-700">P{{ $vo->project->variation_round }}</span>
                        <span class="badge bg-purple-100 text-purple-700">{{ __('tenders.variation_order_badge') }}</span>
                    </td>
                    <td class="px-5 py-4">
                        <a href="{{ route('price-quotes.show', $vo->priceQuote) }}" class="font-mono font-bold text-indigo-600 hover:underline">{{ $vo->priceQuote->number }}</a>
                    </td>
                    <td class="px-5 py-4 hidden md:table-cell text-sm text-slate-600">
                        @if($vo->priceQuote->tender)
                            <a href="{{ route('tenders.show', $vo->priceQuote->tender) }}" class="text-indigo-600 hover:underline">{{ $vo->priceQuote->tender->number }}</a>
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <form action="{{ route('project-reviews.variation-orders.approve', $vo) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-primary btn-sm">
                                    <i class="fa-solid fa-check"></i> {{ __('tenders.project_approve') }}
                                </button>
                            </form>
                            <form action="{{ route('project-reviews.variation-orders.reject', $vo) }}" method="POST"
                                  @submit="if (! confirm('{{ __('tenders.project_reject_confirm') }}')) $event.preventDefault()">
                                @csrf
                                <button type="submit" class="btn-danger btn-sm">
                                    <i class="fa-solid fa-xmark"></i> {{ __('tenders.project_reject') }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
