@extends('layouts.app')

@section('title', $contract->number)
@section('breadcrumb', $contract->number)

@section('content')
@php
    $expiringSoon = $contract->isExpiringSoon();
    $expired      = $contract->isExpired();
@endphp
<div class="page-header">
    <div>
        <h1 class="page-title flex items-center gap-3">
            {{ $contract->number }}
            @if($expiringSoon)
                <span class="badge bg-amber-100 text-amber-700">{{ __('maintenance.contract_expiring_soon') }}</span>
            @elseif($expired)
                <span class="badge bg-rose-100 text-rose-700">{{ __('maintenance.contract_expired') }}</span>
            @endif
        </h1>
        <p class="page-subtitle">{{ $contract->customer?->localized_name }}</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ $contract->url }}" target="_blank" rel="noopener" class="btn-secondary">
            <i class="fa-solid fa-file-pdf"></i>
            {{ __('maintenance.view_contract_file') }}
        </a>
        <a href="{{ route('maintenance-contracts.edit', $contract) }}" class="btn-secondary">
            <i class="fa-solid fa-pen"></i>
            {{ __('app.edit') }}
        </a>
        <a href="{{ route('maintenance-contracts.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
            {{ __('app.back_to_list') }}
        </a>
    </div>
</div>

<div class="card px-6 py-5 mb-5">
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('maintenance.contract_customer') }}</dt>
            <dd class="font-bold text-slate-800">{{ $contract->customer?->localized_name }} ({{ $contract->customer?->code }})</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('maintenance.contract_signed_date') }}</dt>
            <dd class="font-bold text-slate-800">{{ $contract->signed_date->format('Y-m-d') }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('maintenance.contract_expiry_date') }}</dt>
            <dd class="font-bold text-slate-800">{{ $contract->expiry_date->format('Y-m-d') }}</dd>
        </div>
        @if($contract->notes)
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('maintenance.contract_notes') }}</dt>
            <dd class="text-slate-700">{{ $contract->notes }}</dd>
        </div>
        @endif
    </dl>
</div>

<div class="card overflow-hidden">
    <div class="card-header">
        <h3 class="font-bold text-slate-700">{{ __('maintenance.contract_payments') }}</h3>
    </div>
    <div class="px-6 py-5">
        @forelse($contract->payments as $payment)
            <div class="flex items-center justify-between flex-wrap gap-2 py-2.5 border-b border-slate-100 last:border-0">
                <div class="flex items-center gap-3">
                    <span class="font-mono text-sm text-slate-500" dir="ltr">{{ $payment->due_date->format('Y-m-d') }}</span>
                    <span class="font-bold text-slate-800" dir="ltr">{{ number_format($payment->amount, 3) }} {{ $payment->currency?->code }}</span>
                    @if($payment->notes)
                        <span class="text-xs text-slate-400">{{ $payment->notes }}</span>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    @if($payment->invoice_id)
                        <a href="{{ route('accounting.invoices.show', $payment->invoice_id) }}" class="badge bg-emerald-100 text-emerald-700 hover:bg-emerald-200 transition-colors">
                            {{ __('maintenance.payment_status_invoiced') }}
                        </a>
                    @elseif($payment->assignee)
                        <span class="badge bg-violet-100 text-violet-700">{{ $payment->assignee->name }}</span>
                    @else
                        <span class="text-xs text-slate-400">{{ __('maintenance.payment_unassigned') }}</span>
                    @endif
                    @if(! $payment->invoice_id)
                        <form action="{{ route('contract-payments.destroy', [$contract, $payment]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                                <i class="fa-solid fa-trash text-sm"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-400 mb-4">{{ __('maintenance.no_payments') }}</p>
        @endforelse

        <form action="{{ route('contract-payments.store', $contract) }}" method="POST" class="flex items-end gap-3 flex-wrap mt-4 pt-4 border-t border-slate-100">
            @csrf
            <div class="min-w-40">
                <label class="form-label">{{ __('maintenance.payment_due_date') }}</label>
                <input type="date" name="due_date" required class="form-input">
            </div>
            <div class="min-w-32">
                <label class="form-label">{{ __('maintenance.payment_amount') }}</label>
                <input type="number" step="0.001" min="0.001" name="amount" dir="ltr" required class="form-input">
            </div>
            <div class="min-w-40">
                <label class="form-label">{{ __('maintenance.payment_currency') }}</label>
                <select name="currency_id" class="js-select2 form-select" required>
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($currencies as $currency)
                        <option value="{{ $currency->id }}" @selected($currency->is_default)>{{ $currency->localized_name }} ({{ $currency->code }})</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-40">
                <label class="form-label">{{ __('maintenance.payment_notes') }}</label>
                <input type="text" name="notes" class="form-input">
            </div>
            <button type="submit" class="btn-secondary">
                <i class="fa-solid fa-plus"></i>
                {{ __('maintenance.add_payment') }}
            </button>
        </form>
    </div>
</div>
@endsection
