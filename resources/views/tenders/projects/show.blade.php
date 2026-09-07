@extends('layouts.app')

@section('title', $project->number)
@section('breadcrumb', $project->number)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title flex items-center gap-3">
            {{ $project->number }}
            <span class="badge bg-emerald-100 text-emerald-700">{{ __('tenders.project_status_' . $project->status) }}</span>
        </h1>
        <p class="page-subtitle">{{ $project->localized_title }}</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('projects.edit', $project) }}" class="btn-secondary">
            <i class="fa-solid fa-pen"></i>
            {{ __('app.edit') }}
        </a>
        <a href="{{ route('projects.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
            {{ __('app.back_to_list') }}
        </a>
    </div>
</div>

<div class="card px-6 py-5 mb-5">
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.project_customer') }}</dt>
            <dd class="font-bold text-slate-800">{{ $project->customer?->localized_name ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.project_tender') }}</dt>
            <dd class="font-bold text-slate-800">
                @if($project->tender)
                    <a href="{{ route('tenders.show', $project->tender) }}" class="text-indigo-600 hover:underline">{{ $project->tender->number }}</a>
                @else
                    —
                @endif
            </dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.project_created_by') }}</dt>
            <dd class="font-bold text-slate-800">{{ $project->creator?->name ?? '—' }}</dd>
        </div>
        @if($project->notes)
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.tender_notes') }}</dt>
            <dd class="text-slate-700">{{ $project->notes }}</dd>
        </div>
        @endif
    </dl>
</div>

<div class="card overflow-hidden">
    <div class="card-header">
        <h3 class="font-bold text-slate-700">{{ __('external_purchases.purchase_requests') }}</h3>
        <a href="{{ route('purchase-requests.create', ['project_id' => $project->id]) }}" class="btn-primary btn-sm">
            <i class="fa-solid fa-plus"></i>
            {{ __('external_purchases.add_purchase_request') }}
        </a>
    </div>

    @if($project->purchaseRequests->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('external_purchases.request_number') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('external_purchases.request_date') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('external_purchases.request_supplier') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('external_purchases.request_total') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($project->purchaseRequests as $pr)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-3">
                            <a href="{{ route('purchase-requests.show', $pr) }}" class="font-mono font-bold text-indigo-600 hover:underline">{{ $pr->number }}</a>
                        </td>
                        <td class="px-5 py-3 text-slate-600">{{ $pr->date->format('Y-m-d') }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $pr->supplier?->localized_name }}</td>
                        <td class="px-5 py-3 font-bold text-slate-800">{{ number_format($pr->total, 3) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="px-6 py-6 text-sm text-slate-400">{{ __('external_purchases.no_requests') }}</p>
    @endif
</div>
@endsection
