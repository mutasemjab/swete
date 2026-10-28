@extends('layouts.app')

@section('title', __('tenders.x8_show') . ' — ' . $analysis->number)
@section('breadcrumb', $analysis->number)

@section('content')
@php
    $canEditAnalysisItems = auth()->user()->can('analyses.price_analyses.edit') || auth()->user()->can('tenders.price_analyses.edit');
@endphp
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('tenders.x8_show') }}</h1>
        <p class="page-subtitle">{{ $analysis->number }} — {{ $analysis->branch?->localized_name }}</p>
    </div>
    <div class="flex items-center gap-2">
        @if($canEditAnalysisItems)
        <a href="{{ route('price-analyses.x8.edit', $analysis) }}" class="btn-primary">
            <i class="fa-solid fa-pen"></i>
            {{ __('app.edit') }}
        </a>
        @endif
        <a href="{{ route('price-analyses.show', $analysis) }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
            {{ __('app.back_to_list') }}
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success mb-5">{{ session('success') }}</div>
@endif

<div class="card overflow-hidden mb-5">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('x8.col_product') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.analysis_item_model') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.analysis_item_quantity') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">L.P</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.analysis_item_required_discount') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.notes') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('x8.col_status') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($analysis->items as $item)
                @php $status = $item->x8?->approval_status ?? 'pending'; @endphp
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-5 py-3 font-bold text-slate-800">{{ $item->material?->localized_name ?: '—' }}</td>
                    <td class="px-5 py-3 text-slate-600" dir="ltr">{{ $item->ciat_model ?: '—' }}</td>
                    <td class="px-5 py-3 text-slate-600" dir="ltr">{{ number_format((float) $item->quantity, 3) }}</td>
                    <td class="px-5 py-3 text-slate-600" dir="ltr">{{ number_format((float) $item->list_price, 3) }}</td>
                    <td class="px-5 py-3 text-slate-600" dir="ltr">{{ $item->x8?->required_discount_percent !== null ? number_format((float) $item->x8->required_discount_percent, 2) . '%' : '—' }}</td>
                    <td class="px-5 py-3 text-slate-600">
                        @forelse($item->x8?->notes ?? [] as $note)
                            <div class="text-sm">{{ $note }}</div>
                        @empty
                            —
                        @endforelse
                    </td>
                    <td class="px-5 py-3">
                        @if($status === 'approved')
                            <span class="badge bg-emerald-100 text-emerald-700">{{ __('x8.status_approved') }}</span>
                        @elseif($status === 'rejected')
                            <span class="badge bg-rose-100 text-rose-700">{{ __('x8.status_rejected') }}</span>
                        @else
                            <span class="badge bg-amber-100 text-amber-700">{{ __('x8.status_pending') }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@if($analysis->linked_type === 'tender' && $analysis->linked)
@php $tender = $analysis->linked; @endphp
<div class="card overflow-hidden mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700">{{ __('tenders.analysis_attachments_section') }}</h3>
    </div>
    <div class="px-6 py-5">
        @forelse($tender->analysisAttachments as $attachment)
            <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                <a href="{{ $attachment->url }}" target="_blank" rel="noopener" class="text-indigo-600 hover:underline text-sm flex items-center gap-2">
                    <i class="fa-solid fa-paperclip"></i>
                    {{ $attachment->name }}
                </a>
                @if($canEditAnalysisItems)
                <form action="{{ route('tenders.analysis-attachments.destroy', [$tender, $attachment]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                        <i class="fa-solid fa-trash text-sm"></i>
                    </button>
                </form>
                @endif
            </div>
        @empty
            <p class="text-sm text-slate-400 mb-4">{{ __('tenders.no_analysis_attachments') }}</p>
        @endforelse

        @if($canEditAnalysisItems)
        <form action="{{ route('tenders.analysis-attachments.store', $tender) }}" method="POST" enctype="multipart/form-data"
              class="mt-4 pt-4 border-t border-slate-100 space-y-3"
              x-data="{
                nextId: 1,
                rows: [{ id: 0 }],
                addRow() { this.rows.push({ id: this.nextId++ }); },
                removeRow(id) { if (this.rows.length > 1) this.rows = this.rows.filter(r => r.id !== id); },
              }">
            @csrf
            <template x-for="row in rows" :key="row.id">
                <div class="flex items-end gap-3 flex-wrap">
                    <div class="flex-1 min-w-40">
                        <label class="form-label">{{ __('tenders.analysis_attachment_name') }}</label>
                        <input type="text" :name="`attachments[${row.id}][name]`" class="form-input">
                    </div>
                    <div class="flex-1 min-w-56">
                        <label class="form-label">{{ __('tenders.analysis_attachment_file') }}</label>
                        <input type="file" :name="`attachments[${row.id}][file]`" class="form-input">
                    </div>
                    <button type="button" @click="removeRow(row.id)"
                            class="p-2.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all flex-shrink-0">
                        <i class="fa-solid fa-trash text-sm"></i>
                    </button>
                </div>
            </template>
            <div class="flex items-center gap-3">
                <button type="button" @click="addRow()" class="btn-secondary btn-sm">
                    <i class="fa-solid fa-plus"></i>
                    {{ __('tenders.analysis_attachment_add_row') }}
                </button>
                <button type="submit" class="btn-primary btn-sm">
                    <i class="fa-solid fa-upload"></i>
                    {{ __('tenders.analysis_attachment_upload') }}
                </button>
            </div>
        </form>
        @endif
    </div>
</div>
@endif
@endsection
