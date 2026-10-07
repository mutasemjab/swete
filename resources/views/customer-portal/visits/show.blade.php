@extends('layouts.customer-portal')

@section('title', __('customer_portal.visit_review_title'))

@section('bar')
<div class="cp-bar">
    <a href="{{ route('customer-portal.dashboard') }}" class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
    </a>
    <div class="flex-1">
        <p class="font-black leading-tight">{{ __('customer_portal.visit_review_title') }}</p>
        <p class="text-xs text-white/60" dir="ltr">{{ $visit->check_in_at->format('Y-m-d') }}</p>
    </div>
</div>
@endsection

@section('content')

@foreach($visit->reports as $report)
<div class="cp-card">
    <p class="font-black text-slate-800 mb-3">{{ $report->template_name }}</p>

    @if($report->problem)
        <p class="text-sm text-slate-500 mb-1">{{ __('maintenance.report_problem') }}</p>
        <p class="text-sm text-slate-700 mb-3">{{ $report->problem }}</p>
    @endif
    @if($report->solution)
        <p class="text-sm text-slate-500 mb-1">{{ __('maintenance.report_solution') }}</p>
        <p class="text-sm text-slate-700 mb-3">{{ $report->solution }}</p>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        @foreach($report->fields as $field)
            <div>
                <p class="text-xs text-slate-400 mb-0.5">{{ $field->localized_question }}</p>
                @if($field->type === 'images')
                    <div class="flex flex-wrap gap-2 mt-1">
                        @forelse($field->image_urls as $url)
                            <a href="{{ $url }}" target="_blank"><img src="{{ $url }}" class="w-16 h-16 object-cover rounded-lg border border-slate-200"></a>
                        @empty
                            <span class="text-slate-400 text-sm">—</span>
                        @endforelse
                    </div>
                @else
                    <p class="font-semibold text-slate-700 text-sm">{{ $field->formatted_answer ?? '—' }}</p>
                @endif
            </div>
        @endforeach
    </div>

    @if($report->materials->isNotEmpty())
        <div class="mt-3 pt-3 border-t border-slate-100">
            <p class="text-xs text-slate-400 mb-1">{{ __('maintenance.report_materials_used') }}</p>
            @foreach($report->materials as $material)
                <p class="text-sm text-slate-700">{{ $material->material?->localized_name }} × <span dir="ltr">{{ $material->quantity }}</span></p>
            @endforeach
        </div>
    @endif
</div>
@endforeach

@if($visit->isSubmitted())
<div class="cp-card" x-data="{
        drawing: false,
        hasDrawing: false,
        ctx: null,
        init() {
            this.ctx = this.$refs.canvas.getContext('2d');
            this.ctx.strokeStyle = '#1e293b';
            this.ctx.lineWidth = 2.5;
            this.ctx.lineCap = 'round';
        },
        point(e) {
            const rect = this.$refs.canvas.getBoundingClientRect();
            const x = (e.touches ? e.touches[0].clientX : e.clientX) - rect.left;
            const y = (e.touches ? e.touches[0].clientY : e.clientY) - rect.top;
            return { x, y };
        },
        start(e) {
            this.drawing = true;
            this.hasDrawing = true;
            const p = this.point(e);
            this.ctx.beginPath();
            this.ctx.moveTo(p.x, p.y);
        },
        move(e) {
            if (! this.drawing) return;
            const p = this.point(e);
            this.ctx.lineTo(p.x, p.y);
            this.ctx.stroke();
        },
        end() { this.drawing = false; },
        clear() {
            this.ctx.clearRect(0, 0, this.$refs.canvas.width, this.$refs.canvas.height);
            this.hasDrawing = false;
        },
        submit() {
            if (! this.hasDrawing) return;
            this.$refs.canvas.toBlob(blob => {
                const fd = new FormData();
                fd.append('_token', document.querySelector('meta[name=csrf-token]').content);
                fd.append('signature', new File([blob], 'signature.png', { type: 'image/png' }));
                fetch('{{ route('customer-portal.visits.sign', $visit) }}', { method: 'POST', body: fd })
                    .then(() => window.location.reload());
            });
        },
     }" x-init="init()">
    <p class="font-black text-slate-800 mb-2">{{ __('customer_portal.sign_here') }}</p>
    <p class="text-xs text-slate-400 mb-3">{{ __('customer_portal.sign_hint') }}</p>
    <canvas x-ref="canvas" width="600" height="200" class="w-full border-2 border-dashed border-slate-300 rounded-xl bg-slate-50 touch-none"
            @mousedown="start($event)" @mousemove="move($event)" @mouseup="end()" @mouseleave="end()"
            @touchstart.prevent="start($event)" @touchmove.prevent="move($event)" @touchend.prevent="end()"></canvas>
    <div class="flex items-center gap-2 mt-3">
        <button type="button" @click="clear()" class="cp-btn-outline flex-1 justify-center">{{ __('customer_portal.clear_signature') }}</button>
        <button type="button" @click="submit()" :disabled="!hasDrawing" class="cp-btn-primary flex-1 justify-center">
            <i class="fa-solid fa-check"></i>
            {{ __('customer_portal.confirm_and_sign') }}
        </button>
    </div>
</div>
@elseif($visit->signature_path)
<div class="cp-card">
    <p class="font-black text-slate-800 mb-2">{{ __('customer_portal.your_signature') }}</p>
    <img src="{{ $visit->signature_url }}" class="h-20 border border-slate-200 rounded-lg bg-white">
</div>
@endif

@endsection
