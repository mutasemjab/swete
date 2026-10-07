@php
    $colors = [
        'open' => 'slate',
        'submitted_to_customer' => 'amber',
        'customer_signed' => 'violet',
        'closed' => 'emerald',
    ];
    $color = $colors[$status] ?? 'slate';
@endphp
<span class="badge bg-{{ $color }}-100 text-{{ $color }}-700">{{ __('maintenance.visit_status_' . $status) }}</span>
