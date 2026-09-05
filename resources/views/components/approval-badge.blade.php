@php
    $styles = [
        'pending'   => 'bg-amber-100 text-amber-700',
        'approved'  => 'bg-emerald-100 text-emerald-700',
        'rejected'  => 'bg-rose-100 text-rose-700',
        'cancelled' => 'bg-slate-100 text-slate-500',
    ];
@endphp
<span class="badge {{ $styles[$status] ?? $styles['cancelled'] }}">
    {{ __('approvals.status_' . $status) }}
</span>
