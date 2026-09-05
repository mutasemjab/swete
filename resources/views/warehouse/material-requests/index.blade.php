@extends('layouts.app')

@section('title', __('warehouse.material_requests_list'))
@section('breadcrumb', __('warehouse.material_requests'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('warehouse.material_requests_list') }}</h1>
        <p class="page-subtitle">{{ __('warehouse.material_requests_subtitle') }}</p>
    </div>
    <a href="{{ route('warehouse.material-requests.create') }}" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        {{ __('warehouse.add_material_request') }}
    </a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="card px-5 py-4 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-clipboard-list text-emerald-600"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800">{{ $requests->total() }}</p>
            <p class="text-xs text-slate-500 font-medium mt-0.5">{{ __('warehouse.total_material_requests') }}</p>
        </div>
    </div>
</div>

<div class="card overflow-hidden">
    @if($requests->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-clipboard-list text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('warehouse.no_material_requests') }}</p>
            <a href="{{ route('warehouse.material-requests.create') }}" class="btn-primary mt-5">
                <i class="fa-solid fa-plus"></i>
                {{ __('warehouse.add_first_material_request') }}
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.material_request_number') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.material_request_warehouse') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden md:table-cell">{{ __('warehouse.material_request_requester') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.material_request_status') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php
                        $statusStyles = [
                            'draft'            => 'bg-slate-100 text-slate-500',
                            'pending_approval' => 'bg-amber-100 text-amber-700',
                            'approved'         => 'bg-blue-100 text-blue-700',
                            'rejected'         => 'bg-rose-100 text-rose-700',
                            'fulfilled'        => 'bg-emerald-100 text-emerald-700',
                            'cancelled'        => 'bg-slate-100 text-slate-500',
                        ];
                    @endphp
                    @foreach($requests as $materialRequest)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-4">
                            <a href="{{ route('warehouse.material-requests.show', $materialRequest) }}"
                               class="font-mono font-bold text-slate-700 hover:text-indigo-600 transition-colors">{{ $materialRequest->number }}</a>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $materialRequest->warehouse?->localized_name }}</td>
                        <td class="px-5 py-4 hidden md:table-cell text-sm text-slate-600">{{ $materialRequest->requester?->name }}</td>
                        <td class="px-5 py-4">
                            <span class="badge {{ $statusStyles[$materialRequest->status] }}">{{ __('warehouse.material_request_status_' . $materialRequest->status) }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('warehouse.material-requests.show', $materialRequest) }}"
                                   class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="{{ __('app.view') }}">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                {{ __('app.showing') }} <span class="font-bold text-slate-700">{{ $requests->firstItem() }}</span>
                {{ __('app.to') }} <span class="font-bold text-slate-700">{{ $requests->lastItem() }}</span>
                {{ __('app.of') }} <span class="font-bold text-slate-700">{{ $requests->total() }}</span>
                {{ __('app.results') }}
            </p>
            <div class="flex gap-1">
                @if($requests->onFirstPage())
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.previous') }}</span>
                @else
                    <a href="{{ $requests->previousPageUrl() }}" class="btn-secondary btn-sm">{{ __('app.previous') }}</a>
                @endif
                @if($requests->hasMorePages())
                    <a href="{{ $requests->nextPageUrl() }}" class="btn-primary btn-sm">{{ __('app.next') }}</a>
                @else
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.next') }}</span>
                @endif
            </div>
        </div>
        @endif
    @endif
</div>
@endsection
