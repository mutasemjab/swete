@extends('layouts.app')

@section('title', $purchaseRequest->number)
@section('breadcrumb', $purchaseRequest->number)

@php
    $myApproval = $purchaseRequest->approvals->firstWhere('user_id', auth()->id());
    $canDecide  = $myApproval?->decision === 'pending';
@endphp

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title flex items-center gap-3">
            {{ $purchaseRequest->number }}
            <span class="badge bg-{{ $purchaseRequest->status_color }}-100 text-{{ $purchaseRequest->status_color }}-700">{{ __('external_purchases.status_' . $purchaseRequest->status) }}</span>
        </h1>
        <p class="page-subtitle">{{ __('external_purchases.purchase_request') }}</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('purchase-requests.print', $purchaseRequest) }}" target="_blank" class="btn-primary">
            <i class="fa-solid fa-print"></i>
            {{ __('external_purchases.print') }}
        </a>
        @if($purchaseRequest->isEditable())
        <a href="{{ route('purchase-requests.edit', $purchaseRequest) }}" class="btn-secondary">
            <i class="fa-solid fa-pen"></i>
            {{ __('app.edit') }}
        </a>
        @endif
        <a href="{{ route('purchase-requests.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
            {{ __('app.back_to_list') }}
        </a>
    </div>
</div>

<div class="card overflow-hidden mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700">{{ __('external_purchases.approvals') }}</h3>
    </div>
    <div class="px-6 py-5">
        <div class="flex flex-wrap gap-3 mb-4">
            @forelse($purchaseRequest->approvals as $approval)
                <div class="flex items-center gap-2 bg-slate-50 border border-slate-100 rounded-xl px-3 py-2">
                    <span class="w-2 h-2 rounded-full
                        @if($approval->decision === 'approved') bg-emerald-500
                        @elseif($approval->decision === 'rejected') bg-rose-500
                        @else bg-amber-400 @endif"></span>
                    <span class="text-sm font-semibold text-slate-700">{{ $approval->user?->name }}</span>
                    <span class="text-xs text-slate-400">— {{ __('external_purchases.decision_' . $approval->decision) }}</span>
                </div>
            @empty
                <p class="text-sm text-slate-400">{{ __('external_purchases.no_approvers_configured') }}</p>
            @endforelse
        </div>

        @if($purchaseRequest->canApproveManually())
        <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
            <form action="{{ route('purchase-requests.approve-manually', $purchaseRequest) }}" method="POST"
                  onsubmit="return confirm('{{ __('external_purchases.manual_approve_confirm') }}')">
                @csrf
                <button type="submit" class="btn-primary btn-sm">
                    <i class="fa-solid fa-check-double"></i>
                    {{ __('external_purchases.manual_approve') }}
                </button>
            </form>
            <p class="text-xs text-slate-400">{{ __('external_purchases.manual_approve_hint') }}</p>
        </div>
        @endif

        @if($canDecide)
        <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
            <form action="{{ route('purchase-requests.approve', $purchaseRequest) }}" method="POST">
                @csrf
                <button type="submit" class="btn-primary btn-sm">
                    <i class="fa-solid fa-check"></i>
                    {{ __('external_purchases.approve') }}
                </button>
            </form>
            <form action="{{ route('purchase-requests.reject', $purchaseRequest) }}" method="POST"
                  x-data="{ note: '' }">
                @csrf
                <div class="flex items-center gap-2">
                    <input type="text" name="note" x-model="note" placeholder="{{ __('external_purchases.rejection_note_placeholder') }}" class="form-input form-input-sm">
                    <button type="submit" class="btn-danger btn-sm">
                        <i class="fa-solid fa-xmark"></i>
                        {{ __('external_purchases.reject') }}
                    </button>
                </div>
            </form>
        </div>
        @endif
    </div>
</div>

@if($purchaseRequest->status === 'approved')
<div class="card px-6 py-5 mb-5">
    <p class="text-sm text-slate-600 mb-4">{{ __('external_purchases.approved_hint') }}</p>

    @if(! $purchaseRequest->supplier?->email)
        <p class="text-sm text-rose-600 bg-rose-50 border border-rose-100 rounded-xl px-4 py-3 mb-4">
            <i class="fa-solid fa-circle-exclamation"></i>
            {{ __('external_purchases.supplier_email_missing') }}
        </p>
    @endif

    <form action="{{ route('purchase-requests.mark-sent', $purchaseRequest) }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="form-label">{{ __('external_purchases.email_subject') }}</label>
            <input type="text" name="email_subject" value="{{ old('email_subject', $emailTemplate['subject']) }}" dir="ltr"
                   class="form-input @error('email_subject') is-invalid @enderror">
            @error('email_subject')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="form-label">{{ __('external_purchases.email_body') }}</label>
            <textarea name="email_body" rows="6" dir="ltr" class="form-input @error('email_body') is-invalid @enderror">{{ old('email_body', $emailTemplate['body']) }}</textarea>
            @error('email_body')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="btn-primary" @disabled(! $purchaseRequest->supplier?->email)>
            <i class="fa-solid fa-paper-plane"></i>
            {{ __('external_purchases.mark_sent') }}
        </button>
    </form>
</div>
@endif

@if(in_array($purchaseRequest->status, ['sent', 'manufacturing', 'awaiting_price_quotes']))
<div class="card px-6 py-5 mb-5">
    <h3 class="font-bold text-slate-700 mb-4">{{ __('external_purchases.manufacturing_info') }}</h3>
    <form action="{{ route('purchase-requests.manufacturing', $purchaseRequest) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
        @csrf
        <div>
            <label class="form-label">{{ __('external_purchases.so_number') }}</label>
            <input type="text" name="so_number" value="{{ old('so_number', $purchaseRequest->so_number) }}" dir="ltr" class="form-input @error('so_number') is-invalid @enderror">
            @error('so_number')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="form-label">{{ __('external_purchases.ready_date') }}</label>
            <input type="date" name="ready_date" value="{{ old('ready_date', $purchaseRequest->ready_date?->toDateString()) }}" class="form-input @error('ready_date') is-invalid @enderror">
            @error('ready_date')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="btn-secondary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
    </form>
</div>
@endif

@if(in_array($purchaseRequest->status, ['manufacturing', 'awaiting_price_quotes']))
<div class="card overflow-hidden mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700">{{ __('external_purchases.attachments') }}</h3>
    </div>
    <div class="px-6 py-5">
        @forelse($purchaseRequest->attachments as $attachment)
            <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                <a href="{{ $attachment->url }}" target="_blank" rel="noopener" class="text-indigo-600 hover:underline text-sm flex items-center gap-2">
                    <i class="fa-solid fa-link"></i>
                    {{ $attachment->label ?: $attachment->url }}
                </a>
                <form action="{{ route('purchase-requests.attachments.destroy', [$purchaseRequest, $attachment]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                        <i class="fa-solid fa-trash text-sm"></i>
                    </button>
                </form>
            </div>
        @empty
            <p class="text-sm text-slate-400 mb-4">{{ __('external_purchases.no_attachments') }}</p>
        @endforelse

        <form action="{{ route('purchase-requests.attachments.store', $purchaseRequest) }}" method="POST" class="flex items-end gap-3 flex-wrap mt-4 pt-4 border-t border-slate-100">
            @csrf
            <div class="flex-1 min-w-56">
                <label class="form-label">{{ __('external_purchases.attachment_url') }}</label>
                <input type="text" name="url" placeholder="https://..." dir="ltr" class="form-input">
            </div>
            <div class="flex-1 min-w-40">
                <label class="form-label">{{ __('external_purchases.attachment_label') }}</label>
                <input type="text" name="label" class="form-input">
            </div>
            <button type="submit" class="btn-secondary">
                <i class="fa-solid fa-plus"></i>
                {{ __('external_purchases.attachment_add') }}
            </button>
        </form>
    </div>
</div>

<div class="card px-6 py-5 mb-5 flex items-center justify-between">
    <p class="text-sm text-slate-600">{{ __('external_purchases.ship_hint') }}</p>
    <a href="{{ route('purchase-requests.ship', ['purchase_request_ids' => [$purchaseRequest->id]]) }}" class="btn-primary">
        <i class="fa-solid fa-ship"></i>
        {{ __('external_purchases.send_to_shipping_companies') }}
    </a>
</div>
@endif

@if($purchaseRequest->shippingRequests->isNotEmpty())
<div class="card overflow-hidden mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700">{{ __('external_purchases.shipping_requests_sent') }}</h3>
    </div>
    <div class="px-6 py-5 flex flex-wrap gap-2">
        @foreach($purchaseRequest->shippingRequests as $sr)
            <span class="badge bg-violet-100 text-violet-700">{{ $sr->shippingCompany?->localized_name }} — {{ $sr->sent_at->format('Y-m-d') }}</span>
        @endforeach
    </div>
</div>
@endif

<div class="card px-6 py-5 mb-5">
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('external_purchases.request_date') }}</dt>
            <dd class="font-bold text-slate-800">{{ $purchaseRequest->date->format('Y-m-d') }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('external_purchases.request_supplier') }}</dt>
            <dd class="font-bold text-slate-800">{{ $purchaseRequest->supplier?->localized_name }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('external_purchases.request_linked_to') }}</dt>
            <dd class="font-bold text-slate-800">
                @if($purchaseRequest->project)
                    {{ __('external_purchases.link_type_project') }}:
                    <a href="{{ route('projects.show', $purchaseRequest->project) }}" class="text-indigo-600 hover:underline">{{ $purchaseRequest->project->number }}</a>
                @elseif($purchaseRequest->serviceCall)
                    {{ __('external_purchases.link_type_service_call') }}: {{ $purchaseRequest->serviceCall->number }}
                @else
                    {{ __('external_purchases.link_type_stock') }}
                @endif
            </dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('external_purchases.request_branch') }}</dt>
            <dd class="font-bold text-slate-800">{{ $purchaseRequest->branch?->localized_name }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('external_purchases.request_address') }}</dt>
            <dd class="font-bold text-slate-800">
                @forelse($purchaseRequest->request_address_lines as $line)
                    <p>{{ $line }}</p>
                @empty
                    —
                @endforelse
            </dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('external_purchases.request_shipping_address') }}</dt>
            <dd class="font-bold text-slate-800">
                @forelse($purchaseRequest->shipping_address_lines as $line)
                    <p>{{ $line }}</p>
                @empty
                    —
                @endforelse
            </dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('external_purchases.request_location_scope') }}</dt>
            <dd class="font-bold text-slate-800">
                @if($purchaseRequest->location_scope === 'outside_jordan')
                    {{ __('tenders.location_outside_jordan') }} — {{ $purchaseRequest->country?->localized_name }}
                @elseif($purchaseRequest->location_scope === 'inside_jordan')
                    {{ __('tenders.location_inside_jordan') }} — {{ $purchaseRequest->localized_governorate }}
                @else
                    —
                @endif
            </dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('external_purchases.request_currency') }}</dt>
            <dd class="font-bold text-slate-800">{{ $purchaseRequest->currency?->localized_name ?? '—' }}</dd>
        </div>
        @if($purchaseRequest->notes)
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('external_purchases.request_notes') }}</dt>
            <dd class="text-slate-700">{{ $purchaseRequest->notes }}</dd>
        </div>
        @endif
    </dl>
</div>

@if($purchaseRequest->additionalNotes->isNotEmpty())
<div class="card px-6 py-5 mb-5">
    <h3 class="font-bold text-slate-700 mb-3">{{ __('external_purchases.additional_notes') }}</h3>
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        @foreach($purchaseRequest->additionalNotes as $note)
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ $note->label }}</dt>
            <dd class="font-bold text-slate-800">{{ $note->value ?: '—' }}</dd>
        </div>
        @endforeach
    </dl>
</div>
@endif

<div class="card overflow-hidden mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700">{{ __('external_purchases.request_items') }}</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.material') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.voucher_item_quantity') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('external_purchases.item_ercd') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('accounting.invoice_item_unit_price') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('accounting.invoice_item_total') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('external_purchases.item_features') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($purchaseRequest->items as $item)
                <tr>
                    <td class="px-5 py-3">
                        <span class="font-bold text-slate-800">{{ $item->material?->localized_name }}</span>
                        <span class="text-xs text-slate-400 ms-1">{{ $item->material?->unit?->symbol }}</span>
                    </td>
                    <td class="px-5 py-3 text-slate-700">{{ number_format($item->quantity, 3) }}</td>
                    <td class="px-5 py-3 text-slate-700">{{ $item->ercd ?? '—' }}</td>
                    <td class="px-5 py-3 text-slate-700">{{ number_format($item->unit_price, 3) }}</td>
                    <td class="px-5 py-3 font-bold text-slate-800">{{ number_format($item->total, 3) }}</td>
                    <td class="px-5 py-3 text-slate-600 text-xs">
                        @forelse($item->features as $feature)
                            <span class="badge bg-slate-100 text-slate-600 me-1 mb-1">{{ $feature->value }}</span>
                        @empty
                            —
                        @endforelse
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="flex justify-end">
    <div class="card px-6 py-5 w-full sm:w-80">
        <div class="flex justify-between pt-2">
            <dt class="text-slate-700 font-bold">{{ __('external_purchases.request_total') }}</dt>
            <dd class="font-black text-lg text-cyan-700">{{ number_format($purchaseRequest->total, 3) }}</dd>
        </div>
    </div>
</div>
@endsection
