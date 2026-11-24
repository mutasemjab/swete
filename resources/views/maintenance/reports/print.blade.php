<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
<meta charset="UTF-8">
<title>{{ $report->number }}</title>
<style>
    :root { --mr-teal: #0f766e; --mr-border: #2b2b2b; }

    @page { size: A4; margin: 14mm 12mm; }
    @media print { .mr-toolbar { display: none; } }

    * { box-sizing: border-box; }

    body {
        font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
        color: #1a1a1a;
        margin: 0;
        padding: 0;
        font-size: 12px;
        background: #f1f1f1;
    }

    .mr-page {
        width: 210mm;
        min-height: 297mm;
        margin: 10mm auto;
        padding: 14mm 12mm;
        background: #fff;
        box-shadow: 0 0 8px rgba(0,0,0,.15);
    }

    .mr-toolbar {
        max-width: 210mm;
        margin: 0 auto 10px;
        text-align: center;
    }

    .mr-toolbar button {
        background: var(--mr-teal);
        color: #fff;
        border: none;
        padding: 9px 22px;
        font-size: 13px;
        font-weight: 700;
        border-radius: 6px;
        cursor: pointer;
    }

    h1 { font-size: 20px; color: var(--mr-teal); margin: 0 0 4px; }
    h2 { font-size: 13px; color: var(--mr-teal); border-bottom: 2px solid var(--mr-teal); padding-bottom: 4px; margin: 18px 0 10px; }
    .mr-meta { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 10px; }
    .mr-meta div span { display: block; color: #777; font-size: 10px; }
    .mr-meta div strong { font-size: 12.5px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: start; font-size: 11.5px; }
    th { background: #f3f4f6; }
    .mr-approvals { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 16px; }
    .mr-approvals div { border: 1px solid #ddd; border-radius: 6px; padding: 10px; }
</style>
</head>
<body>

<div class="mr-toolbar">
    <button onclick="window.print()">{{ __('app.print') }}</button>
</div>

<div class="mr-page">
    <h1>{{ $report->number }}</h1>
    <p style="color:#777;margin:0 0 14px;">{{ $report->template_name }}</p>

    <div class="mr-meta">
        <div><span>{{ __('maintenance.report_customer') }}</span><strong>{{ $report->customer?->localized_name }}</strong></div>
        <div><span>{{ __('maintenance.report_product') }}</span><strong>{{ $report->material?->localized_name ?? '—' }}</strong></div>
        <div><span>{{ __('maintenance.report_date') }}</span><strong>{{ $report->date->format('Y-m-d') }}</strong></div>
    </div>

    @if($report->problem)
        <h2>{{ __('maintenance.report_problem') }}</h2>
        <p>{{ $report->problem }}</p>
    @endif
    @if($report->solution)
        <h2>{{ __('maintenance.report_solution') }}</h2>
        <p>{{ $report->solution }}</p>
    @endif

    <h2>{{ __('maintenance.template_fields') }}</h2>
    <table>
        <tbody>
            @foreach($report->fields as $field)
                @if($field->type !== 'images')
                <tr>
                    <td style="width:55%">{{ $field->localized_question }}</td>
                    <td>{{ $field->formatted_answer ?? __('maintenance.answer_not_answered') }}</td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    @if($report->materials->isNotEmpty())
        <h2>{{ __('maintenance.report_materials_used') }}</h2>
        <table>
            <thead><tr><th>{{ __('maintenance.report_material') }}</th><th>{{ __('maintenance.report_quantity') }}</th></tr></thead>
            <tbody>
                @foreach($report->materials as $item)
                <tr><td>{{ $item->material?->localized_name }}</td><td>{{ number_format($item->quantity, 3) }}</td></tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($report->notes)
        <h2>{{ __('maintenance.report_notes') }}</h2>
        <p>{{ $report->notes }}</p>
    @endif

    <div class="mr-approvals">
        <div>
            <span style="color:#777;font-size:10px;">{{ __('maintenance.foreman_note') }}</span>
            <p style="margin:4px 0 0;">{{ $report->foremanReviewer?->name }} — {{ $report->foreman_reviewed_at?->format('Y-m-d') }}</p>
        </div>
        <div>
            <span style="color:#777;font-size:10px;">{{ __('maintenance.content_status_approved') }}</span>
            <p style="margin:4px 0 0;">{{ $report->approver?->name }} — {{ $report->approved_at?->format('Y-m-d H:i') }}</p>
        </div>
    </div>
</div>

</body>
</html>
