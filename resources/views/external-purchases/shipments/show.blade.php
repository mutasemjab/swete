@extends('layouts.app')

@section('title', $shipment->number)
@section('breadcrumb', $shipment->number)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title flex items-center gap-3">
            {{ $shipment->number }}
            <span class="badge bg-cyan-100 text-cyan-700">{{ __('external_purchases.transport_mode_' . $shipment->transport_mode) }}</span>
        </h1>
        <p class="page-subtitle">{{ __('external_purchases.shipment') }}</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('shipments.edit', $shipment) }}" class="btn-secondary">
            <i class="fa-solid fa-pen"></i>
            {{ __('app.edit') }}
        </a>
        <a href="{{ route('shipments.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
            {{ __('app.back_to_list') }}
        </a>
    </div>
</div>

<div class="card px-6 py-5 mb-5">
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('external_purchases.shipment_shipping_company') }}</dt>
            <dd class="font-bold text-slate-800">{{ $shipment->shippingCompany?->localized_name }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('external_purchases.shipment_transport_mode') }}</dt>
            <dd class="font-bold text-slate-800">
                {{ __('external_purchases.transport_mode_' . $shipment->transport_mode) }}
                @if($shipment->sea_service_type) — {{ __('external_purchases.sea_service_' . $shipment->sea_service_type) }} @endif
                @if($shipment->air_service_type) — {{ __('external_purchases.air_service_' . $shipment->air_service_type) }} @endif
            </dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('external_purchases.shipment_incoterm') }}</dt>
            <dd class="font-bold text-slate-800">{{ $shipment->incoterm ? strtoupper($shipment->incoterm) . ' — ' . __('external_purchases.incoterm_' . $shipment->incoterm) : '—' }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('external_purchases.shipment_price') }}</dt>
            <dd class="font-bold text-slate-800">{{ $shipment->price ? number_format($shipment->price, 3) . ' ' . $shipment->currency?->code : '—' }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('external_purchases.shipment_hazardous') }}</dt>
            <dd class="font-bold text-slate-800">
                @if($shipment->is_hazardous)
                    <span class="badge bg-rose-100 text-rose-700">{{ __('app.yes') }}</span>
                @else
                    <span class="badge bg-emerald-100 text-emerald-700">{{ __('app.no') }}</span>
                @endif
            </dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('external_purchases.shipment_shipping_line') }}</dt>
            <dd class="font-bold text-slate-800">{{ $shipment->shipping_line ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('external_purchases.shipment_bol_number') }}</dt>
            <dd class="font-bold text-slate-800" dir="ltr">{{ $shipment->bill_of_lading_number ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('external_purchases.shipment_container_number') }}</dt>
            <dd class="font-bold text-slate-800" dir="ltr">{{ $shipment->container_number ?? '—' }}</dd>
        </div>
        @if($shipment->notes)
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('external_purchases.shipment_notes') }}</dt>
            <dd class="text-slate-700">{{ $shipment->notes }}</dd>
        </div>
        @endif
    </dl>
</div>

<div class="card overflow-hidden">
    <div class="card-header">
        <h3 class="font-bold text-slate-700">{{ __('external_purchases.shipment_purchase_requests') }}</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('external_purchases.request_number') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('external_purchases.request_supplier') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('external_purchases.request_date') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($shipment->purchaseRequests as $pr)
                <tr>
                    <td class="px-5 py-3">
                        <a href="{{ route('purchase-requests.show', $pr) }}" class="font-mono font-bold text-indigo-600 hover:underline">{{ $pr->number }}</a>
                    </td>
                    <td class="px-5 py-3 text-slate-700">{{ $pr->supplier?->localized_name }}</td>
                    <td class="px-5 py-3 text-slate-700">{{ $pr->date->format('Y-m-d') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
