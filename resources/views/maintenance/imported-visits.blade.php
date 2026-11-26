@extends('layouts.app')
@section('title', __('maintenance.imported_visits'))
@section('breadcrumb', __('maintenance.imported_visits'))
@section('content')
<h1 class="page-title mb-4">{{ __('maintenance.imported_visits') }}</h1>
<p class="mb-4 text-slate-500">{{ __('maintenance.imported_visits_note') }}</p>
<div class="card overflow-x-auto"><table class="w-full text-sm">
<thead><tr><th class="p-3">{{ __('maintenance.report_number') }}</th><th>{{ __('maintenance.contract_customer') }}</th><th>{{ __('maintenance.contract_equipment') }}</th><th>{{ __('maintenance.import_scheduled_date') }}</th><th>{{ __('maintenance.import_actual_date') }}</th><th>{{ __('app.status') }}</th><th>{{ __('maintenance.import_source_notes') }}</th></tr></thead>
<tbody>@forelse($visits ?? [] as $visit)
@php($source = json_decode($visit->payload, true))
<tr class="border-t"><td class="p-3">{{ $visit->number }}@if($visit->report_id)<br><a class="text-indigo-600" href="{{ route('maintenance-reports.show', $visit->report_id) }}">{{ __('maintenance.import_open_report') }}</a>@endif</td>
<td>{{ $visit->customer_name }}</td><td><a href="{{ route('equipment-units.show', $visit->equipment_unit_id) }}">{{ $visit->unit_name }}</a></td><td>{{ $visit->scheduled_date }}</td><td>{{ $visit->actual_date ?? '—' }}</td><td>{{ $visit->status }}<br>{{ $visit->visit_type }}</td><td class="p-3 whitespace-pre-wrap">{{ $source['description'] }}<br>{{ $source['notes'] }}</td></tr>
@empty<tr><td class="p-5" colspan="7">{{ __('maintenance.unit_no_history') }}</td></tr>@endforelse
</tbody></table></div>
@if($visits){{ $visits->links() }}@endif
@endsection
