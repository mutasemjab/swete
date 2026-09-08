@extends('layouts.app')

@section('title', __('external_purchases.send_to_shipping_companies'))
@section('breadcrumb', __('external_purchases.send_to_shipping_companies'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('external_purchases.send_to_shipping_companies') }}</h1>
        <p class="page-subtitle">{{ __('external_purchases.ship_page_subtitle') }}</p>
    </div>
    <a href="{{ route('purchase-requests.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<p class="text-sm text-slate-500 bg-amber-50 border border-amber-100 rounded-xl px-4 py-3 mb-5">
    <i class="fa-solid fa-circle-info text-amber-500"></i>
    {{ __('external_purchases.ship_attachments_hint') }}
</p>

<form action="{{ route('purchase-requests.ship.send') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-truck-ramp-box text-cyan-500 text-sm"></i>
                {{ __('external_purchases.purchase_requests') }}
            </h3>
        </div>
        <div class="px-6 py-5">
            @if($eligiblePurchaseRequests->isEmpty())
                <p class="text-sm text-slate-400">{{ __('external_purchases.no_manufacturing_requests') }}</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($eligiblePurchaseRequests as $pr)
                        <label class="flex items-start gap-3 border border-slate-200 rounded-xl px-4 py-3 cursor-pointer hover:border-indigo-300 transition-colors">
                            <input type="checkbox" name="purchase_request_ids[]" value="{{ $pr->id }}"
                                   class="mt-1" @checked($selectedIds->contains($pr->id) || collect(old('purchase_request_ids'))->contains($pr->id))>
                            <span>
                                <span class="block font-bold text-slate-800">{{ $pr->number }}</span>
                                <span class="block text-xs text-slate-400">{{ $pr->supplier?->localized_name }} — {{ $pr->date->format('Y-m-d') }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('purchase_request_ids')<p class="form-error mt-2"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            @endif
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-ship text-cyan-500 text-sm"></i>
                {{ __('external_purchases.shipping_companies') }}
            </h3>
        </div>
        <div class="px-6 py-5">
            @if($shippingCompanies->isEmpty())
                <p class="text-sm text-slate-400">{{ __('external_purchases.no_shipping_companies') }}</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($shippingCompanies as $company)
                        <label class="flex items-start gap-3 border border-slate-200 rounded-xl px-4 py-3 cursor-pointer hover:border-indigo-300 transition-colors">
                            <input type="checkbox" name="shipping_company_ids[]" value="{{ $company->id }}"
                                   class="mt-1" @checked(collect(old('shipping_company_ids'))->contains($company->id))>
                            <span>
                                <span class="block font-bold text-slate-800">{{ $company->localized_name }}</span>
                                <span class="block text-xs text-slate-400" dir="ltr">{{ $company->email }}</span>
                                @if($company->country)
                                    <span class="block text-xs text-slate-400">{{ $company->country->localized_name }}</span>
                                @endif
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('shipping_company_ids')<p class="form-error mt-2"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            @endif
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-paperclip text-cyan-500 text-sm"></i>
                {{ __('external_purchases.ship_attachments') }}
            </h3>
        </div>
        <div class="px-6 py-5">
            <input type="file" name="attachments[]" multiple class="form-input @error('attachments') is-invalid @enderror @error('attachments.*') is-invalid @enderror">
            @error('attachments')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            @error('attachments.*')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-comment text-cyan-500 text-sm"></i>
                {{ __('external_purchases.ship_message') }}
            </h3>
        </div>
        <div class="px-6 py-5">
            <textarea name="message" rows="4" class="form-input @error('message') is-invalid @enderror">{{ old('message') }}</textarea>
            @error('message')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-paper-plane"></i>
            {{ __('external_purchases.send_now') }}
        </button>
        <a href="{{ route('purchase-requests.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
