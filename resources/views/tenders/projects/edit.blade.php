@extends('layouts.app')

@section('title', __('tenders.edit_project'))
@section('breadcrumb', __('tenders.edit_project'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('tenders.edit_project') }}</h1>
        <p class="page-subtitle">{{ $project->number }}</p>
    </div>
    <a href="{{ route('projects.show', $project) }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('projects.update', $project) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-diagram-project text-orange-500 text-sm"></i>
                {{ __('tenders.project') }}
            </h3>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="form-label">{{ __('tenders.project_title') }} <span class="text-rose-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $project->title) }}"
                       class="form-input @error('title') is-invalid @enderror">
                @error('title')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('app.name_en') }}</label>
                <input type="text" name="title_en" value="{{ old('title_en', $project->title_en) }}" dir="ltr"
                       class="form-input @error('title_en') is-invalid @enderror">
                @error('title_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('tenders.project_status') }} <span class="text-rose-500">*</span></label>
                <select name="status" class="form-select @error('status') is-invalid @enderror">
                    <option value="active" @selected(old('status', $project->status) === 'active')>{{ __('tenders.project_status_active') }}</option>
                    <option value="completed" @selected(old('status', $project->status) === 'completed')>{{ __('tenders.project_status_completed') }}</option>
                    <option value="cancelled" @selected(old('status', $project->status) === 'cancelled')>{{ __('tenders.project_status_cancelled') }}</option>
                </select>
                @error('status')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div class="sm:col-span-2">
                <label class="form-label">{{ __('tenders.tender_notes') }}</label>
                <textarea name="notes" rows="3" class="form-input @error('notes') is-invalid @enderror">{{ old('notes', $project->notes) }}</textarea>
                @error('notes')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ route('projects.show', $project) }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
