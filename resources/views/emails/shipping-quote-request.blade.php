@php $isRtl = app()->isLocale('ar'); $numbers = $purchaseRequests->pluck('number')->implode(', '); @endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
<meta charset="UTF-8">
<title>{{ __('external_purchases.ship_email_subject', ['number' => $numbers]) }}</title>
</head>
<body style="font-family: Tahoma, Arial, sans-serif; color:#1a1a1a; font-size:14px; line-height:1.7;">
    <p>{{ __('external_purchases.ship_email_greeting', ['company' => $shippingCompany->localized_name]) }}</p>

    <p>{{ __('external_purchases.ship_email_intro', ['number' => $numbers]) }}</p>

    @if($purchaseRequests->count() > 1)
        <ul>
            @foreach($purchaseRequests as $pr)
                <li>{{ $pr->number }}</li>
            @endforeach
        </ul>
    @endif

    @if($message)
        <p style="white-space: pre-line;">{{ $message }}</p>
    @endif

    @if(count($files))
        <p><strong>{{ __('external_purchases.ship_email_attachments') }}:</strong></p>
        <ul>
            @foreach($files as $file)
                <li>{{ $file->getClientOriginalName() }}</li>
            @endforeach
        </ul>
    @endif

    <p>{{ __('external_purchases.ship_email_signoff') }}</p>
</body>
</html>
