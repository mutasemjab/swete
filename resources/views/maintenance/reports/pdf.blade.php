<style>
body {font-family:dejavusans;font-size:10pt;color:#203442} h1 {color:#0f766e;font-size:18pt} h2 {font-size:12pt;color:#0f766e;border-bottom:1px solid #d4dfe4;padding:6px 0} table {border-collapse:collapse;width:100%;margin:8px 0} td,th {border:1px solid #d4dfe4;padding:7px;vertical-align:top} th {background:#edf5f4} .value {color:#b45309} .note {white-space:pre-wrap} .signature {height:60px;max-width:180px} .photo {max-width:150mm;max-height:120mm} small {color:#60717d}
</style>
<h1 dir="rtl">{{ $original ? 'نسخة الفني الموقعة' : 'تقرير الصيانة المعتمد' }}</h1>
<small>{{ $original ? 'Original signed technician report' : 'Approved maintenance report' }} · {{ $report->number }}</small>
<table>
<tr><th>Client / العميل</th><td>{{ $original ? ($snapshot['customer_name'] ?? '') : $report->customer?->localized_name }}</td><th>Date / التاريخ</th><td>{{ $report->date->format('Y-m-d') }}</td></tr>
<tr><th>Unit / الجهاز</th><td>{{ $snapshot['unit_name'] ?? $report->equipmentUnit?->localized_name }}</td><th>Model / الموديل</th><td>{{ $snapshot['model'] ?? $report->equipmentUnit?->model?->model_number }}</td></tr>
<tr><th>Serial / التسلسلي</th><td>{{ $snapshot['serial'] ?? $report->equipmentUnit?->serial_number }}</td><th>Site / الموقع</th><td>{{ $snapshot['site'] ?? $report->equipmentUnit?->location }}</td></tr>
<tr><th>Type / النوع</th><td>{{ $snapshot['maintenance_type'] ?? '' }}</td><th>Template / النموذج</th><td>{{ $report->template_name }}</td></tr>
</table>
<h2 dir="rtl">الفحص والقراءات / Checklist &amp; readings</h2>
<table><thead><tr><th>Field / البند</th><th>Value / القيمة</th></tr></thead><tbody>
@foreach($original ? ($snapshot['fields'] ?? []) : $report->fields as $field)
    @if(data_get($field, 'type') !== 'images')
    <tr><td>{{ data_get($field, 'question_en') }}<br><span dir="rtl">{{ data_get($field, 'question') }}</span></td><td class="value">{{ data_get($field, 'answer') }}</td></tr>
    @endif
@endforeach
</tbody></table>
@foreach(['problem' => 'Problem / المشكلة', 'solution' => 'Work performed / العمل المنفذ', 'notes' => 'Notes / ملاحظات'] as $key => $label)
@php($value = $original ? ($snapshot[$key] ?? '') : $report->{$key})
@if($value)<h2>{{ $label }}</h2><p class="note" dir="auto">{{ $value }}</p>@endif
@endforeach
@unless($original)
@if($report->materials->isNotEmpty())
<h2>Materials / المواد</h2><table>@foreach($report->materials as $material)<tr><td>{{ $material->material?->localized_name }}</td><td>{{ $material->quantity }}</td></tr>@endforeach</table>
@endif
<h2>Approval / الاعتماد</h2>
<table><tr><th>Foreman / الفورمان</th><td>{{ $report->foremanReviewer?->name }} · {{ $report->foreman_reviewed_at?->format('Y-m-d H:i') }}<br>{{ $report->foreman_note }}</td></tr><tr><th>Engineer / المهندس</th><td>{{ $report->approver?->name }} · {{ $report->approved_at?->format('Y-m-d H:i') }}</td></tr></table>
@endunless
@if($original)
@if(! empty($snapshot['materials']))
<h2>Materials / المواد</h2><table>@foreach($snapshot['materials'] as $material)<tr><td>{{ $material['name'] }}</td><td>{{ $material['quantity'] }}</td></tr>@endforeach</table>
@endif
<h2>Signatures / التوقيعات</h2><table><tr>
@foreach(['technician' => 'Technician / الفني', 'customer' => 'Customer / العميل'] as $key => $label)
<td>{{ $label }}<br>{{ data_get($snapshot, $key.'_signature.name') }}<br>
@if($signature = data_get($snapshot, $key.'_signature.path'))<img class="signature" src="{{ \App\Support\MaintenanceReportPdf::image($signature) }}">@endif
<br><small>{{ data_get($snapshot, $key.'_signature.signed_at') }}</small></td>
@endforeach
</tr></table>
@endif
@php($pdfPhotos = $original ? ($snapshot['photos'] ?? []) : $report->fields->where('type', 'images')->flatMap(fn ($field) => collect(json_decode($field->answer ?? '[]', true) ?: [])->map(fn ($path) => ['path' => $path]))->all())
@foreach($pdfPhotos as $photo)
<div style="page-break-inside:avoid"><h2>Site photo / صورة الموقع</h2><img class="photo" src="{{ \App\Support\MaintenanceReportPdf::image($photo['path']) }}"><p dir="auto">{{ $photo['caption'] ?? '' }}</p></div>
@endforeach
