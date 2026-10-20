@extends('layouts.app')

@section('title', __('app.edit') . ' — ' . $field->label_ar)
@section('breadcrumb', __('app.edit'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('app.edit') }}</h1>
        <p class="page-subtitle">{{ $field->label_ar }}</p>
    </div>
    <a href="{{ route('client-extra-fields.' . $field->entity_type . 's') }}"
       class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('client-extra-fields.update', $field) }}" method="POST">
    @csrf
    @method('PUT')
    <input type="hidden" name="entity_type" value="{{ $field->entity_type }}">

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-sliders text-amber-500 text-sm"></i>
                {{ __("clients.{$field->entity_type}_extra_fields") }}
            </h3>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="form-label">{{ __('clients.field_label_ar') }} <span class="text-rose-500">*</span></label>
                <input type="text" name="label_ar" value="{{ old('label_ar', $field->label_ar) }}"
                       class="form-input @error('label_ar') is-invalid @enderror">
                @error('label_ar')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">{{ __('clients.field_label_en') }}</label>
                <input type="text" name="label_en" value="{{ old('label_en', $field->label_en) }}" dir="ltr"
                       class="form-input @error('label_en') is-invalid @enderror">
                @error('label_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">{{ __('clients.field_type') }} <span class="text-rose-500">*</span></label>
                <select name="field_type" class="form-select @error('field_type') is-invalid @enderror">
                    @foreach($fieldTypes as $type)
                        <option value="{{ $type }}" @selected(old('field_type', $field->field_type) === $type)>
                            {{ __("clients.field_type_{$type}") }}
                        </option>
                    @endforeach
                </select>
                @error('field_type')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">{{ __('clients.field_sort_order') }}</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $field->sort_order) }}" min="0" dir="ltr"
                       class="form-input @error('sort_order') is-invalid @enderror">
                @error('sort_order')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
            <div class="flex items-center">
                <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                    <input type="hidden" name="status" value="0">
                    <input type="checkbox" name="status" value="1" class="sr-only peer"
                           @checked(old('status', $field->status))>
                    <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-indigo-400 rounded-full peer
                                peer-checked:bg-indigo-600 transition-all
                                after:content-[''] after:absolute after:top-0.5 after:start-[2px]
                                after:bg-white after:rounded-full after:h-5 after:w-5
                                after:transition-all peer-checked:after:translate-x-full"></div>
                    <span class="ms-3 text-sm font-semibold text-slate-700">{{ __('app.active') }}</span>
                </label>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ route('client-extra-fields.' . $field->entity_type . 's') }}"
           class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
