@extends('layouts.app')

@section('title', $project->number)
@section('breadcrumb', $project->number)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title flex items-center gap-3">
            {{ $project->number }}
            @php $statusColor = ['active' => 'emerald', 'under_review' => 'amber', 'completed' => 'indigo', 'cancelled' => 'slate'][$project->status] ?? 'slate'; @endphp
            <span class="badge bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700">{{ __('tenders.project_status_' . $project->status) }}</span>
        </h1>
        <p class="page-subtitle">{{ $project->localized_title }}</p>
        @if($project->review_reason)
            <p class="text-sm text-amber-600 mt-2"><i class="fa-solid fa-triangle-exclamation"></i> {{ __('tenders.project_review_reason_notice') }} {{ $project->review_reason }}</p>
        @endif
    </div>
    <div class="flex items-center gap-2">
        @if($project->status === 'under_review')
            <form action="{{ route('project-reviews.approve', $project) }}" method="POST">
                @csrf
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-check"></i>
                    {{ __('tenders.project_approve') }}
                </button>
            </form>
            <form action="{{ route('project-reviews.reject', $project) }}" method="POST"
                  @submit="if (! confirm('{{ __('tenders.project_reject_confirm') }}')) $event.preventDefault()">
                @csrf
                <button type="submit" class="btn-danger">
                    <i class="fa-solid fa-xmark"></i>
                    {{ __('tenders.project_reject') }}
                </button>
            </form>
        @endif
        <a href="{{ route('projects.edit', $project) }}" class="btn-secondary">
            <i class="fa-solid fa-pen"></i>
            {{ __('app.edit') }}
        </a>
        <a href="{{ $project->status === 'under_review' ? route('project-reviews.index') : route('projects.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
            {{ __('app.back_to_list') }}
        </a>
    </div>
</div>

<div class="card px-6 py-5 mb-5">
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.project_customer') }}</dt>
            <dd class="font-bold text-slate-800">{{ $project->customer?->localized_name ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.project_tender') }}</dt>
            <dd class="font-bold text-slate-800">
                @if($project->tender)
                    <a href="{{ route('tenders.show', $project->tender) }}" class="text-indigo-600 hover:underline">{{ $project->tender->number }}</a>
                @else
                    —
                @endif
            </dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.project_created_by') }}</dt>
            <dd class="font-bold text-slate-800">{{ $project->creator?->name ?? '—' }}</dd>
        </div>
        @if($project->notes)
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.tender_notes') }}</dt>
            <dd class="text-slate-700">{{ $project->notes }}</dd>
        </div>
        @endif
    </dl>
</div>

<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-chart-pie text-emerald-500 text-sm"></i>
            {{ __('tenders.financial_analysis_section') }}
        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-slate-50 rounded-xl px-4 py-3">
            <p class="text-xs text-slate-400 font-medium mb-1">{{ __('tenders.financial_project_value') }}</p>
            <p class="text-xl font-black text-slate-800" dir="ltr">{{ number_format($financialSummary['value'], 3) }}</p>
        </div>
        <div class="bg-emerald-50 rounded-xl px-4 py-3">
            <p class="text-xs text-emerald-600 font-medium mb-1">{{ __('tenders.financial_paid') }}</p>
            <p class="text-xl font-black text-emerald-700" dir="ltr">{{ number_format($financialSummary['paid'], 3) }}</p>
        </div>
        <div class="bg-rose-50 rounded-xl px-4 py-3">
            <p class="text-xs text-rose-600 font-medium mb-1">{{ __('tenders.financial_due') }}</p>
            <p class="text-xl font-black text-rose-700" dir="ltr">{{ number_format($financialSummary['due'], 3) }}</p>
        </div>
    </div>
</div>

<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-users text-indigo-500 text-sm"></i>
            {{ __('tenders.project_team_section') }}
        </h3>
    </div>
    <div class="px-6 py-5">
        @php $canAssignTeam = auth()->id() === $project->manager_id; @endphp
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4 text-sm">
            <div>
                <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.project_manager') }}</dt>
                <dd class="font-bold text-slate-800">{{ $project->manager?->name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.project_engineer') }}</dt>
                <dd class="font-bold text-slate-800">{{ $project->engineer?->name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.project_foreman') }}</dt>
                <dd class="font-bold text-slate-800">{{ $project->foreman?->name ?? '—' }}</dd>
            </div>
        </div>
        @if($canAssignTeam)
        <form action="{{ route('projects.assign-team', $project) }}" method="POST" class="flex items-end gap-3 flex-wrap pt-4 border-t border-slate-100">
            @csrf
            <div class="flex-1 min-w-48">
                <label class="form-label">{{ __('tenders.project_engineer') }}</label>
                <select name="engineer_id" class="js-select2 form-select">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" @selected($project->engineer_id == $employee->id)>{{ $employee->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-48">
                <label class="form-label">{{ __('tenders.project_foreman') }}</label>
                <select name="foreman_id" class="js-select2 form-select">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" @selected($project->foreman_id == $employee->id)>{{ $employee->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-primary btn-sm">
                <i class="fa-solid fa-floppy-disk"></i>
                {{ __('app.save') }}
            </button>
        </form>
        @endif
    </div>
</div>

<div class="card overflow-hidden mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700">{{ __('tenders.project_quote_items_section') }}</h3>
    </div>
    @if($quoteItems->isEmpty())
        <p class="px-6 py-6 text-sm text-slate-400">{{ __('tenders.no_project_quote_items') }}</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.quote_item_quote_number') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.quote_item_material') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.quote_item_quantity') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.quote_item_unit_price') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.quote_item_assigned_to') }}</th>
                        <th class="px-5 py-3 w-56"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($quoteItems as $item)
                    @php $assignment = $item->assignments->firstWhere('project_id', $project->id); @endphp
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-3 font-mono text-xs text-slate-500">{{ $item->quoteNumber }}</td>
                        <td class="px-5 py-3 text-sm font-bold text-slate-800">{{ $item->material?->localized_name ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm text-slate-600" dir="ltr">{{ number_format($item->quantity, 3) }}</td>
                        <td class="px-5 py-3 text-sm text-slate-600" dir="ltr">{{ number_format($item->unit_price, 3) }}</td>
                        <td class="px-5 py-3 text-sm">
                            @if($assignment)
                                <span class="font-bold text-slate-700">{{ $assignment->assignee?->name }}</span>
                                @if($assignment->is_done)
                                    <span class="badge bg-emerald-100 text-emerald-700 ms-1">{{ __('tenders.quote_item_done') }}</span>
                                @endif
                            @else
                                <span class="text-slate-400">{{ __('tenders.quote_item_unassigned') }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-2">
                                @if(! $assignment?->is_done)
                                    <form action="{{ route('projects.item-assignments.store', $project) }}" method="POST" class="flex items-center gap-1.5">
                                        @csrf
                                        <input type="hidden" name="price_quote_item_id" value="{{ $item->id }}">
                                        <select name="assigned_to" class="js-select2 form-select" style="min-width:140px">
                                            @foreach($employees as $employee)
                                                <option value="{{ $employee->id }}" @selected($assignment?->assigned_to == $employee->id)>{{ $employee->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn-secondary btn-sm">{{ __('tenders.quote_item_assign') }}</button>
                                    </form>
                                @endif
                                @if($assignment && ! $assignment->is_done)
                                    <form action="{{ route('projects.item-assignments.mark-done', [$project, $assignment]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn-primary btn-sm">{{ __('tenders.quote_item_mark_done') }}</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<div class="card overflow-hidden mb-5">
    <div class="card-header flex items-center justify-between">
        <h3 class="font-bold text-slate-700">{{ __('tenders.project_agreements_section') }}</h3>
        <a href="{{ route('agreements.create', ['project_id' => $project->id]) }}" class="btn-secondary btn-sm">
            <i class="fa-solid fa-plus"></i>
            {{ __('tenders.add_project_agreement') }}
        </a>
    </div>
    <div class="px-6 py-5">
        @forelse($project->agreements as $agreement)
            <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                <a href="{{ $agreement->url }}" target="_blank" rel="noopener" class="text-indigo-600 hover:underline text-sm flex items-center gap-2">
                    <i class="fa-solid fa-file-contract"></i>
                    {{ $agreement->number }}
                </a>
                <span class="text-xs text-slate-400">{{ $agreement->created_at->format('Y-m-d') }}</span>
            </div>
        @empty
            <p class="text-sm text-slate-400">{{ __('tenders.no_project_agreements') }}</p>
        @endforelse
    </div>
</div>



<div class="card overflow-hidden mt-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700">{{ __('tenders.project_attachments') }}</h3>
    </div>
    <div class="px-6 py-5">
        @forelse($project->attachments as $attachment)
            <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                <a href="{{ $attachment->url }}" target="_blank" rel="noopener" class="text-indigo-600 hover:underline text-sm flex items-center gap-2">
                    <i class="fa-solid fa-paperclip"></i>
                    {{ $attachment->name }}
                </a>
                <form action="{{ route('projects.attachments.destroy', [$project, $attachment]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                        <i class="fa-solid fa-trash text-sm"></i>
                    </button>
                </form>
            </div>
        @empty
            <p class="text-sm text-slate-400 mb-4">{{ __('tenders.no_project_attachments') }}</p>
        @endforelse

        <form action="{{ route('projects.attachments.store', $project) }}" method="POST" enctype="multipart/form-data"
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
                        <label class="form-label">{{ __('tenders.project_attachment_name') }}</label>
                        <input type="text" :name="`attachments[${row.id}][name]`" class="form-input">
                    </div>
                    <div class="flex-1 min-w-56">
                        <label class="form-label">{{ __('tenders.project_attachment_file') }}</label>
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
                    {{ __('tenders.project_attachment_add_row') }}
                </button>
                <button type="submit" class="btn-primary btn-sm">
                    <i class="fa-solid fa-upload"></i>
                    {{ __('tenders.project_attachment_upload') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
