@php $isRtl = app()->isLocale('ar'); $contract = $scheduledVisit->contract; @endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
<meta charset="UTF-8">
<title>{{ __('maintenance.visit_due_email_subject', ['number' => $contract->number]) }}</title>
</head>
<body style="font-family: Tahoma, Arial, sans-serif; color:#1a1a1a; font-size:14px; line-height:1.7;">
    <p>{{ __('maintenance.visit_due_email_intro') }}</p>

    <ul>
        <li><strong>{{ __('maintenance.contract_number') }}:</strong> {{ $contract->number }}</li>
        <li><strong>{{ __('maintenance.contract_customer') }}:</strong> {{ $contract->customer?->localized_name }}</li>
        <li><strong>{{ __('maintenance.scheduled_visit_date') }}:</strong> {{ $scheduledVisit->scheduled_date->format('Y-m-d') }}</li>
        <li><strong>{{ __('maintenance.scheduled_visit_type') }}:</strong> {{ __('maintenance.scheduled_visit_type_' . $scheduledVisit->type) }}</li>
        @if($scheduledVisit->notes)
            <li><strong>{{ __('maintenance.payment_notes') }}:</strong> {{ $scheduledVisit->notes }}</li>
        @endif
    </ul>
</body>
</html>
