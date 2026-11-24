@extends('layouts.app')

@section('title', $report->number)
@section('breadcrumb', $report->number)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title flex items-center gap-3">
            {{ $report->number }}
            @php($contentColors = ['received' => 'slate', 'engineer' => 'amber', 'approved' => 'emerald'])
            <span class="badge bg-{{ $contentColors[$report->content_status] }}-100 text-{{ $contentColors[$report->content_status] }}-700">{{ __('maintenance.content_status_'.$report->content_status) }}</span>
        </h1>
        <p class="page-subtitle">{{ $report->template_name }}</p>
    </div>
    <div class="flex items-center gap-2">
        @if($report->isContentApproved())
            <a href="{{ route('maintenance-reports.print', $report) }}" target="_blank" class="btn-secondary">
                <i class="fa-solid fa-print"></i>
                {{ __('maintenance.print_report') }}
            </a>
        @endif
        @if($report->content_status === 'received')
            @can('maintenance.reports.review')
            <form action="{{ route('maintenance-reports.send-to-engineer', $report) }}" method="POST" class="flex items-center gap-2" x-data="{ note: '' }">
                @csrf
                <input type="text" name="foreman_note" x-model="note" class="form-input !py-2 !text-sm w-44" placeholder="{{ __('maintenance.foreman_note_placeholder') }}">
                <button type="submit" class="btn-secondary">
                    <i class="fa-solid fa-arrow-right"></i> {{ __('maintenance.send_to_engineer') }}
                </button>
            </form>
            @endcan
        @elseif($report->content_status === 'engineer')
            @can('maintenance.reports.approve')
            <form action="{{ route('maintenance-reports.approve-content', $report) }}" method="POST">
                @csrf
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-check"></i> {{ __('maintenance.approve_content') }}
                </button>
            </form>
            @endcan
        @endif
        @if($report->price_quote_id)
            <a href="{{ route('price-quotes.show', $report->price_quote_id) }}" class="btn-secondary">
                <i class="fa-solid fa-file-invoice"></i>
                {{ $report->priceQuote?->number }}
            </a>
        @else
            <form action="{{ route('maintenance-reports.convert-to-quote', $report) }}" method="POST">
                @csrf
                <button type="submit" class="btn-secondary" title="{{ __('maintenance.convert_to_quote_hint') }}">
                    <i class="fa-solid fa-file-invoice"></i>
                    {{ __('maintenance.convert_to_quote') }}
                </button>
            </form>
        @endif
        @if($report->canEditContent(Auth::user()))
        <a href="{{ route('maintenance-reports.edit', $report) }}" class="btn-secondary">
            <i class="fa-solid fa-pen"></i>
            {{ __('app.edit') }}
        </a>
        @endif
        @include('components.back-link', ['url' => $backUrl])
    </div>
</div>

<div class="card px-6 py-5 mb-5">
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('maintenance.report_customer') }}</dt>
            <dd class="font-bold text-slate-800">{{ $report->customer?->localized_name }} ({{ $report->customer?->code }})</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('maintenance.report_product') }}</dt>
            <dd class="font-bold text-slate-800">{{ $report->material?->localized_name ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('maintenance.report_date') }}</dt>
            <dd class="font-bold text-slate-800">{{ $report->date->format('Y-m-d') }}</dd>
        </div>
        @if($report->equipmentUnit)
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('equipment.unit') }}</dt>
            <dd class="font-bold text-slate-800"><a href="{{ route('equipment-units.show', $report->equipmentUnit) }}" class="hover:text-indigo-600">{{ $report->equipmentUnit->localized_name }}</a></dd>
        </div>
        @endif
        @if($report->problem)
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('maintenance.report_problem') }}</dt>
            <dd class="text-slate-700">{{ $report->problem }}</dd>
        </div>
        @endif
        @if($report->solution)
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('maintenance.report_solution') }}</dt>
            <dd class="text-slate-700">{{ $report->solution }}</dd>
        </div>
        @endif
        @if($report->notes)
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('maintenance.report_notes') }}</dt>
            <dd class="text-slate-700">{{ $report->notes }}</dd>
        </div>
        @endif
        @if($report->foreman_note)
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('maintenance.foreman_note') }} ({{ $report->foremanReviewer?->name }})</dt>
            <dd class="text-slate-700">{{ $report->foreman_note }}</dd>
        </div>
        @endif
        @if($report->isContentApproved())
        <div class="sm:col-span-3 text-xs text-slate-400">
            {{ __('maintenance.approved_by_on', ['user' => $report->approver?->name, 'date' => $report->approved_at?->format('Y-m-d H:i')]) }}
        </div>
        @endif
    </dl>
</div>

<div class="card overflow-hidden mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700">{{ __('maintenance.template_fields') }}</h3>
    </div>
    <div class="divide-y divide-slate-100">
        @foreach($report->fields as $field)
        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
            <dt class="text-slate-500 font-medium sm:col-span-2">{{ $field->localized_question }}</dt>
            <dd class="font-bold text-slate-800">
                @if($field->type === 'images')
                    @if($field->image_urls)
                        <div class="flex flex-wrap gap-2">
                            @foreach($field->image_urls as $url)
                                <a href="{{ $url }}" target="_blank"><img src="{{ $url }}" class="w-20 h-20 object-cover rounded-lg border border-slate-200"></a>
                            @endforeach
                        </div>
                    @else
                        <span class="font-normal text-slate-400">{{ __('maintenance.report_no_images') }}</span>
                    @endif
                @else
                    {{ $field->formatted_answer ?? __('maintenance.answer_not_answered') }}
                @endif
            </dd>
        </div>
        @endforeach
    </div>
</div>

<div class="card overflow-hidden">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-boxes-stacked text-teal-500 text-sm"></i>
            {{ __('maintenance.report_materials_used') }}
        </h3>
        @if($report->materials_approval_status !== 'none')
            <span class="badge
                @if($report->materials_approval_status === 'approved') bg-emerald-100 text-emerald-700
                @elseif($report->materials_approval_status === 'rejected') bg-rose-100 text-rose-700
                @else bg-amber-100 text-amber-700 @endif">
                {{ __('maintenance.materials_approval_status_' . $report->materials_approval_status) }}
            </span>
        @endif
    </div>
    <div class="px-6 py-5">
        @if($report->materials->isEmpty())
            <p class="text-sm text-slate-400">{{ __('maintenance.report_no_materials') }}</p>
        @else
            <table class="w-full text-sm mb-4">
                <thead>
                    <tr class="text-start text-xs font-black text-slate-500 uppercase tracking-wider">
                        <th class="text-start py-1.5">{{ __('maintenance.report_material') }}</th>
                        <th class="text-start py-1.5">{{ __('maintenance.report_quantity') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($report->materials as $item)
                    <tr>
                        <td class="py-2 text-slate-700">{{ $item->material?->localized_name }} ({{ $item->material?->code }})</td>
                        <td class="py-2 font-bold text-slate-800" dir="ltr">{{ number_format($item->quantity, 3) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($report->issueVoucher)
                <a href="{{ route('warehouse.vouchers.show', ['type' => 'issue', 'voucher' => $report->issue_voucher_id]) }}" class="text-sm text-indigo-600 hover:underline">
                    <i class="fa-solid fa-arrow-up-from-bracket"></i>
                    {{ __('maintenance.view_issue_voucher') }}
                </a>
            @endif

            @if($report->materialApprovals->isNotEmpty())
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <p class="text-xs font-bold text-slate-500 mb-2">{{ __('maintenance.materials_approvers') }}</p>
                    <ul class="space-y-1.5">
                        @foreach($report->materialApprovals as $approval)
                        <li class="flex items-center justify-between text-sm">
                            <span class="text-slate-700">{{ $approval->user?->name }}</span>
                            @if($approval->decision === 'pending')
                                <span class="badge bg-amber-100 text-amber-700">{{ __('maintenance.materials_approval_status_pending') }}</span>
                            @elseif($approval->decision === 'approved')
                                <span class="badge bg-emerald-100 text-emerald-700">{{ __('maintenance.materials_approval_status_approved') }}</span>
                            @else
                                <span class="badge bg-rose-100 text-rose-700" title="{{ $approval->note }}">{{ __('maintenance.materials_approval_status_rejected') }}</span>
                            @endif
                        </li>
                        @endforeach
                    </ul>

                    @php $myApproval = $report->materialApprovals->firstWhere('user_id', Auth::id()); @endphp
                    @if($myApproval && $myApproval->decision === 'pending')
                        <div class="flex items-center gap-2 mt-4" x-data="{ showReject: false, note: '' }">
                            <form action="{{ route('maintenance-reports.approve-materials', $report) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-primary btn-sm">
                                    <i class="fa-solid fa-check"></i> {{ __('maintenance.approve_materials') }}
                                </button>
                            </form>
                            <button type="button" @click="showReject = true" class="btn-danger btn-sm" x-show="!showReject">
                                <i class="fa-solid fa-xmark"></i> {{ __('maintenance.reject_materials') }}
                            </button>
                            <form action="{{ route('maintenance-reports.reject-materials', $report) }}" method="POST" class="flex items-center gap-2" x-show="showReject" x-cloak>
                                @csrf
                                <input type="text" name="note" x-model="note" class="form-input !py-1.5 !text-sm" placeholder="{{ __('maintenance.reject_materials_note') }}">
                                <button type="submit" class="btn-danger btn-sm">{{ __('maintenance.reject_materials') }}</button>
                            </form>
                        </div>
                    @endif
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
