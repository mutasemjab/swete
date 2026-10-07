@extends('layouts.app')

@section('title', __('maintenance.visit_details'))
@section('breadcrumb', __('maintenance.visit_details'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title flex items-center gap-3">
            {{ $visit->customer?->localized_name }}
            @include('maintenance.visits._status-badge', ['status' => $visit->status])
        </h1>
        <p class="page-subtitle">{{ $visit->technician?->name }} — <span dir="ltr">{{ $visit->check_in_at->format('Y-m-d H:i') }}</span></p>
    </div>
    <a href="{{ route('maintenance-visits.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<div class="card px-6 py-5 mb-5">
    <dl class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-sm">
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('maintenance.visit_check_in_at') }}</dt>
            <dd class="font-bold text-slate-800" dir="ltr">{{ $visit->check_in_at->format('Y-m-d H:i') }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('maintenance.visit_check_out_at') }}</dt>
            <dd class="font-bold text-slate-800" dir="ltr">{{ $visit->check_out_at?->format('Y-m-d H:i') ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('maintenance.visit_duration') }}</dt>
            <dd class="font-bold text-slate-800" dir="ltr">{{ $visit->duration ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('maintenance.visit_signed_at') }}</dt>
            <dd class="font-bold text-slate-800" dir="ltr">{{ $visit->signed_at?->format('Y-m-d H:i') ?? '—' }}</dd>
        </div>
        @if($visit->signature_path)
        <div class="sm:col-span-4">
            <dt class="text-slate-400 font-medium mb-1">{{ __('maintenance.visit_signature') }}</dt>
            <dd><img src="{{ $visit->signature_url }}" class="h-20 border border-slate-200 rounded-lg bg-white"></dd>
        </div>
        @endif
    </dl>
</div>

@foreach($visit->reports as $report)
<div class="card overflow-hidden mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700">{{ $report->template_name }}</h3>
        <span class="text-xs font-mono text-slate-400" dir="ltr">{{ $report->number }}</span>
    </div>
    <div class="px-6 py-5 space-y-4 text-sm">
        @if($report->problem)
        <div><dt class="text-slate-400 font-medium mb-0.5">{{ __('maintenance.report_problem') }}</dt><dd class="text-slate-700">{{ $report->problem }}</dd></div>
        @endif
        @if($report->solution)
        <div><dt class="text-slate-400 font-medium mb-0.5">{{ __('maintenance.report_solution') }}</dt><dd class="text-slate-700">{{ $report->solution }}</dd></div>
        @endif
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach($report->fields as $field)
            <div>
                <dt class="text-slate-400 font-medium mb-0.5">{{ $field->localized_question }}</dt>
                @if($field->type === 'images')
                    <dd class="flex flex-wrap gap-2 mt-1">
                        @forelse($field->image_urls as $url)
                            <a href="{{ $url }}" target="_blank"><img src="{{ $url }}" class="w-16 h-16 object-cover rounded-lg border border-slate-200"></a>
                        @empty
                            <span class="text-slate-400">—</span>
                        @endforelse
                    </dd>
                @else
                    <dd class="font-semibold text-slate-700">{{ $field->formatted_answer ?? '—' }}</dd>
                @endif
            </div>
            @endforeach
        </div>
        @if($report->materials->isNotEmpty())
        <div>
            <dt class="text-slate-400 font-medium mb-1">{{ __('maintenance.report_materials_used') }}</dt>
            <dd class="space-y-1">
                @foreach($report->materials as $material)
                    <div class="flex items-center gap-2 text-slate-700">
                        <span class="font-semibold">{{ $material->material?->localized_name }}</span>
                        <span class="text-slate-400">×</span>
                        <span dir="ltr">{{ $material->quantity }}</span>
                    </div>
                @endforeach
            </dd>
        </div>
        @endif
    </div>
</div>
@endforeach

@if($visit->status === 'customer_signed')
<div class="card mb-5" x-data="{
        visitTypeId: '',
        types: {{ $visitTypes->map(fn ($t) => ['id' => $t->id, 'name' => $t->localized_name, 'requiresNote' => $t->requires_note])->values()->toJson() }},
        get requiresNote() { const t = this.types.find(x => String(x.id) === String(this.visitTypeId)); return t ? t.requiresNote : false; },
     }">
    <div class="card-header">
        <h3 class="font-bold text-slate-700">{{ __('maintenance.visit_classify') }}</h3>
    </div>
    <form action="{{ route('maintenance-visits.classify', $visit) }}" method="POST" class="px-6 py-5 space-y-4">
        @csrf
        <div>
            <label class="form-label">{{ __('maintenance.visit_type') }} <span class="text-rose-500">*</span></label>
            <select name="visit_type_id" x-model="visitTypeId" class="form-select @error('visit_type_id') is-invalid @enderror" required>
                <option value="">{{ __('app.select') }}</option>
                @foreach($visitTypes as $visitType)
                    <option value="{{ $visitType->id }}">{{ $visitType->localized_name }}</option>
                @endforeach
            </select>
            @error('visit_type_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>
        <div x-show="requiresNote" x-cloak>
            <label class="form-label">{{ __('maintenance.visit_classification_note') }}</label>
            <textarea name="note" rows="3" class="form-input @error('note') is-invalid @enderror">{{ old('note') }}</textarea>
            @error('note')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-check"></i>
            {{ __('maintenance.visit_mark_done') }}
        </button>
    </form>
</div>
@elseif($visit->status === 'closed')
<div class="card px-6 py-5 mb-5">
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('maintenance.visit_type') }}</dt>
            <dd class="font-bold text-slate-800">{{ $visit->visitType?->localized_name }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('maintenance.visit_classified_by') }}</dt>
            <dd class="font-bold text-slate-800">{{ $visit->classifiedBy?->name }}</dd>
        </div>
        @if($visit->classification_note)
        <div class="sm:col-span-2">
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('maintenance.visit_classification_note') }}</dt>
            <dd class="text-slate-700">{{ $visit->classification_note }}</dd>
        </div>
        @endif
    </dl>
</div>
@endif
@endsection
