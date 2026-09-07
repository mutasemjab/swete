@extends('layouts.app')

@section('title', $purchaseRequest->number)
@section('breadcrumb', $purchaseRequest->number)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $purchaseRequest->number }}</h1>
        <p class="page-subtitle">{{ __('external_purchases.purchase_request') }}</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('purchase-requests.print', $purchaseRequest) }}" target="_blank" class="btn-primary">
            <i class="fa-solid fa-print"></i>
            {{ __('external_purchases.print') }}
        </a>
        <a href="{{ route('purchase-requests.edit', $purchaseRequest) }}" class="btn-secondary">
            <i class="fa-solid fa-pen"></i>
            {{ __('app.edit') }}
        </a>
        <a href="{{ route('purchase-requests.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
            {{ __('app.back_to_list') }}
        </a>
    </div>
</div>

<div class="card px-6 py-5 mb-5">
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('external_purchases.request_date') }}</dt>
            <dd class="font-bold text-slate-800">{{ $purchaseRequest->date->format('Y-m-d') }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('external_purchases.request_supplier') }}</dt>
            <dd class="font-bold text-slate-800">{{ $purchaseRequest->supplier?->localized_name }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('external_purchases.request_linked_to') }}</dt>
            <dd class="font-bold text-slate-800">
                @if($purchaseRequest->project)
                    {{ __('external_purchases.link_type_project') }}:
                    <a href="{{ route('projects.show', $purchaseRequest->project) }}" class="text-indigo-600 hover:underline">{{ $purchaseRequest->project->number }}</a>
                @elseif($purchaseRequest->serviceCall)
                    {{ __('external_purchases.link_type_service_call') }}: {{ $purchaseRequest->serviceCall->number }}
                @else
                    {{ __('external_purchases.link_type_stock') }}
                @endif
            </dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('external_purchases.request_branch') }}</dt>
            <dd class="font-bold text-slate-800">{{ $purchaseRequest->branch?->localized_name }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('external_purchases.request_address') }}</dt>
            <dd class="font-bold text-slate-800">
                @forelse($purchaseRequest->request_address_lines as $line)
                    <p>{{ $line }}</p>
                @empty
                    —
                @endforelse
            </dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('external_purchases.request_shipping_address') }}</dt>
            <dd class="font-bold text-slate-800">
                @forelse($purchaseRequest->shipping_address_lines as $line)
                    <p>{{ $line }}</p>
                @empty
                    —
                @endforelse
            </dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('external_purchases.request_location_scope') }}</dt>
            <dd class="font-bold text-slate-800">
                @if($purchaseRequest->location_scope === 'outside_jordan')
                    {{ __('tenders.location_outside_jordan') }} — {{ $purchaseRequest->country?->localized_name }}
                @elseif($purchaseRequest->location_scope === 'inside_jordan')
                    {{ __('tenders.location_inside_jordan') }} — {{ $purchaseRequest->localized_governorate }}
                @else
                    —
                @endif
            </dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('external_purchases.request_currency') }}</dt>
            <dd class="font-bold text-slate-800">{{ $purchaseRequest->currency?->localized_name ?? '—' }}</dd>
        </div>
        @if($purchaseRequest->notes)
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('external_purchases.request_notes') }}</dt>
            <dd class="text-slate-700">{{ $purchaseRequest->notes }}</dd>
        </div>
        @endif
    </dl>
</div>

<div class="card overflow-hidden mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700">{{ __('external_purchases.request_items') }}</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.material') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.voucher_item_quantity') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('accounting.invoice_item_unit_price') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('accounting.invoice_item_total') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($purchaseRequest->items as $item)
                <tr>
                    <td class="px-5 py-3">
                        <span class="font-bold text-slate-800">{{ $item->material?->localized_name }}</span>
                        <span class="text-xs text-slate-400 ms-1">{{ $item->material?->unit?->symbol }}</span>
                    </td>
                    <td class="px-5 py-3 text-slate-700">{{ number_format($item->quantity, 3) }}</td>
                    <td class="px-5 py-3 text-slate-700">{{ number_format($item->unit_price, 3) }}</td>
                    <td class="px-5 py-3 font-bold text-slate-800">{{ number_format($item->total, 3) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="flex justify-end">
    <div class="card px-6 py-5 w-full sm:w-80">
        <div class="flex justify-between pt-2">
            <dt class="text-slate-700 font-bold">{{ __('external_purchases.request_total') }}</dt>
            <dd class="font-black text-lg text-cyan-700">{{ number_format($purchaseRequest->total, 3) }}</dd>
        </div>
    </div>
</div>
@endsection
