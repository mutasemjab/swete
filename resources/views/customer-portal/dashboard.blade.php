@extends('layouts.customer-portal')

@section('title', __('customer_portal.dashboard_title'))

@section('bar')
<div class="cp-bar">
    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
        <i class="fa-solid fa-screwdriver-wrench"></i>
    </div>
    <div class="flex-1">
        <p class="font-black leading-tight">{{ $customer->localized_name }}</p>
        <p class="text-xs text-white/60">{{ __('customer_portal.dashboard_title') }}</p>
    </div>
    <form method="POST" action="{{ route('customer-portal.logout') }}">
        @csrf
        <button type="submit" class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0 active:bg-white/20" title="{{ __('app.logout') }}">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
        </button>
    </form>
</div>
@endsection

@section('content')

@if($visitsAwaitingReview->isNotEmpty())
<div class="cp-card !bg-amber-50 !border-amber-200">
    <p class="font-black text-amber-800 mb-3">
        <i class="fa-solid fa-signature"></i>
        {{ __('customer_portal.visits_awaiting_review') }}
    </p>
    <div class="space-y-2">
        @foreach($visitsAwaitingReview as $visit)
            <a href="{{ route('customer-portal.visits.show', $visit) }}" class="flex items-center justify-between bg-white rounded-xl px-4 py-3 hover:shadow-sm transition-all">
                <span class="font-semibold text-slate-700" dir="ltr">{{ $visit->check_in_at->format('Y-m-d') }}</span>
                <span class="cp-btn-primary !px-3 !py-1.5 !text-xs">{{ __('customer_portal.review_and_sign') }}</span>
            </a>
        @endforeach
    </div>
</div>
@endif

<div class="cp-card">
    <div class="flex items-center justify-between mb-4">
        <p class="font-black text-slate-800">{{ __('customer_portal.my_contracts') }}</p>
    </div>
    @forelse($contracts as $contract)
        <a href="{{ route('customer-portal.contracts.show', $contract) }}" class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0 hover:bg-slate-50/50 -mx-2 px-2 rounded-lg transition-colors">
            <div>
                <p class="font-bold text-slate-800" dir="ltr">{{ $contract->number }}</p>
                <p class="text-xs text-slate-400" dir="ltr">{{ $contract->signed_date->format('Y-m-d') }} → {{ $contract->expiry_date->format('Y-m-d') }}</p>
            </div>
            <i class="fa-solid fa-chevron-{{ app()->isLocale('ar') ? 'left' : 'right' }} text-slate-300"></i>
        </a>
    @empty
        <p class="text-sm text-slate-400">{{ __('customer_portal.no_contracts') }}</p>
    @endforelse
</div>

<div class="cp-card">
    <div class="flex items-center justify-between mb-4">
        <p class="font-black text-slate-800">{{ __('customer_portal.my_requests') }}</p>
        <a href="{{ route('customer-portal.maintenance-requests.create') }}" class="cp-btn-primary !px-4 !py-2 !text-xs">
            <i class="fa-solid fa-plus"></i>
            {{ __('customer_portal.new_request') }}
        </a>
    </div>
    @forelse($maintenanceRequests as $maintenanceRequest)
        <div class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0">
            <div>
                <p class="text-sm text-slate-700">{{ \Illuminate\Support\Str::limit($maintenanceRequest->description, 70) }}</p>
                <p class="text-xs text-slate-400">{{ $maintenanceRequest->created_at->diffForHumans() }}</p>
            </div>
            @if($maintenanceRequest->status === 'pending')
                <span class="badge bg-amber-100 text-amber-700">{{ __('maintenance.request_status_pending') }}</span>
            @elseif($maintenanceRequest->status === 'approved')
                <span class="badge bg-emerald-100 text-emerald-700">{{ __('maintenance.request_status_approved') }}</span>
            @else
                <span class="badge bg-rose-100 text-rose-700">{{ __('maintenance.request_status_rejected') }}</span>
            @endif
        </div>
    @empty
        <p class="text-sm text-slate-400">{{ __('customer_portal.no_requests') }}</p>
    @endforelse
</div>

@if($pastVisits->isNotEmpty())
<div class="cp-card">
    <p class="font-black text-slate-800 mb-4">{{ __('customer_portal.past_visits') }}</p>
    @foreach($pastVisits as $visit)
        <a href="{{ route('customer-portal.visits.show', $visit) }}" class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0 hover:bg-slate-50/50 -mx-2 px-2 rounded-lg transition-colors">
            <span class="text-sm text-slate-700" dir="ltr">{{ $visit->check_in_at->format('Y-m-d') }}</span>
            @include('maintenance.visits._status-badge', ['status' => $visit->status])
        </a>
    @endforeach
</div>
@endif

@endsection
