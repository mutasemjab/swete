@extends('layouts.customer-portal')

@section('title', $contract->number)

@section('bar')
<div class="cp-bar">
    <a href="{{ route('customer-portal.dashboard') }}" class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
    </a>
    <div class="flex-1">
        <p class="font-black leading-tight" dir="ltr">{{ $contract->number }}</p>
    </div>
</div>
@endsection

@section('content')

<div class="cp-card">
    <dl class="grid grid-cols-2 gap-4 text-sm">
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('maintenance.contract_signed_date') }}</dt>
            <dd class="font-bold text-slate-800" dir="ltr">{{ $contract->signed_date->format('Y-m-d') }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('maintenance.contract_expiry_date') }}</dt>
            <dd class="font-bold text-slate-800" dir="ltr">{{ $contract->expiry_date->format('Y-m-d') }}</dd>
        </div>
    </dl>
</div>

<div class="cp-card">
    <p class="font-black text-slate-800 mb-3">{{ __('maintenance.contract_payments') }}</p>
    @forelse($contract->payments as $payment)
        <div class="flex items-center justify-between py-2.5 border-b border-slate-100 last:border-0">
            <span class="text-sm text-slate-600" dir="ltr">{{ $payment->due_date->format('Y-m-d') }}</span>
            <span class="font-bold text-slate-800" dir="ltr">{{ number_format($payment->amount, 3) }} {{ $payment->currency?->code }}</span>
        </div>
    @empty
        <p class="text-sm text-slate-400">{{ __('maintenance.no_payments') }}</p>
    @endforelse
</div>

<div class="cp-card">
    <p class="font-black text-slate-800 mb-3">{{ __('maintenance.contract_scheduled_visits') }}</p>
    @forelse($contract->scheduledVisits as $scheduledVisit)
        <div class="flex items-center justify-between py-2.5 border-b border-slate-100 last:border-0">
            <span class="text-sm text-slate-600" dir="ltr">{{ $scheduledVisit->scheduled_date->format('Y-m-d') }}</span>
            <span class="badge {{ $scheduledVisit->type === 'emergency' ? 'bg-rose-100 text-rose-700' : 'bg-blue-100 text-blue-700' }}">
                {{ __('maintenance.scheduled_visit_type_' . $scheduledVisit->type) }}
            </span>
        </div>
    @empty
        <p class="text-sm text-slate-400">{{ __('maintenance.no_scheduled_visits') }}</p>
    @endforelse
</div>

@endsection
