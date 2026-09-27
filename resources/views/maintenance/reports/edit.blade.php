@extends('layouts.app')

@section('title', __('maintenance.edit_report'))
@section('breadcrumb', __('maintenance.edit_report'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('maintenance.edit_report') }}</h1>
        <p class="page-subtitle">{{ $report->number }} — {{ $report->template_name }}</p>
    </div>
    <a href="{{ route('maintenance-reports.show', $report) }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('maintenance-reports.update', $report) }}" method="POST" x-data x-init="$nextTick(() => window.initSelect2())">
    @csrf
    @method('PUT')

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-clipboard-list text-teal-500 text-sm"></i>
                {{ __('maintenance.edit_report') }}
            </h3>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="form-label">{{ __('maintenance.report_customer') }} <span class="text-rose-500">*</span></label>
                <select name="customer_id" class="js-select2 form-select @error('customer_id') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" @selected(old('customer_id', $report->customer_id) == $customer->id)>{{ $customer->localized_name }} ({{ $customer->code }})</option>
                    @endforeach
                </select>
                @error('customer_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('maintenance.report_date') }} <span class="text-rose-500">*</span></label>
                <input type="date" name="date" value="{{ old('date', $report->date->toDateString()) }}"
                       class="form-input @error('date') is-invalid @enderror">
                @error('date')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div class="sm:col-span-2">
                <label class="form-label">{{ __('maintenance.report_notes') }}</label>
                <textarea name="notes" rows="2" class="form-input @error('notes') is-invalid @enderror">{{ old('notes', $report->notes) }}</textarea>
                @error('notes')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-list-check text-teal-500 text-sm"></i>
                {{ __('maintenance.template_fields') }}
            </h3>
        </div>
        <div class="px-6 py-5 space-y-4">
            @foreach($report->fields as $field)
            <div>
                <label class="form-label">{{ $field->localized_question }}</label>

                @if($field->type === 'text')
                    <input type="text" name="answers[{{ $field->id }}]" value="{{ old("answers.{$field->id}", $field->answer) }}"
                           class="form-input @error("answers.{$field->id}") is-invalid @enderror">
                @elseif($field->type === 'number')
                    <input type="number" step="0.001" dir="ltr" name="answers[{{ $field->id }}]" value="{{ old("answers.{$field->id}", $field->answer) }}"
                           class="form-input @error("answers.{$field->id}") is-invalid @enderror">
                @elseif($field->type === 'boolean')
                    <select name="answers[{{ $field->id }}]" class="form-select @error("answers.{$field->id}") is-invalid @enderror">
                        <option value="">{{ __('app.select') }}</option>
                        <option value="1" @selected(old("answers.{$field->id}", $field->answer) === '1')>{{ __('app.yes') }}</option>
                        <option value="0" @selected(old("answers.{$field->id}", $field->answer) === '0')>{{ __('app.no') }}</option>
                    </select>
                @elseif($field->type === 'choice')
                    <select name="answers[{{ $field->id }}]" class="form-select @error("answers.{$field->id}") is-invalid @enderror">
                        <option value="">{{ __('app.select') }}</option>
                        @foreach($field->options ?? [] as $option)
                            <option value="{{ $option }}" @selected(old("answers.{$field->id}", $field->answer) === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                @endif
                @error("answers.{$field->id}")<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
            @endforeach
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ route('maintenance-reports.show', $report) }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
