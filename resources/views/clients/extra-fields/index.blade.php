@extends('layouts.app')

@section('title', $entityType === 'customer' ? __('clients.customer_extra_fields') : __('clients.supplier_extra_fields'))
@section('breadcrumb', $entityType === 'customer' ? __('clients.customer_extra_fields') : __('clients.supplier_extra_fields'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $entityType === 'customer' ? __('clients.customer_extra_fields') : __('clients.supplier_extra_fields') }}</h1>
        <p class="page-subtitle">{{ $entityType === 'customer' ? __('clients.extra_fields_subtitle_customer') : __('clients.extra_fields_subtitle_supplier') }}</p>
    </div>
</div>

<div class="card mb-6">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-plus text-indigo-500 text-sm"></i>
            {{ __('clients.add_extra_field') }}
        </h3>
    </div>
    <form action="{{ route('client-extra-fields.store') }}" method="POST">
        @csrf
        <input type="hidden" name="entity_type" value="{{ $entityType }}">
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div>
                <label class="form-label">{{ __('clients.field_label_ar') }} <span class="text-rose-500">*</span></label>
                <input type="text" name="label_ar" value="{{ old('label_ar') }}"
                       class="form-input @error('label_ar') is-invalid @enderror">
                @error('label_ar')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">{{ __('clients.field_label_en') }}</label>
                <input type="text" name="label_en" value="{{ old('label_en') }}" dir="ltr"
                       class="form-input @error('label_en') is-invalid @enderror">
                @error('label_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">{{ __('clients.field_type') }} <span class="text-rose-500">*</span></label>
                <select name="field_type" class="form-select @error('field_type') is-invalid @enderror">
                    @foreach($fieldTypes as $type)
                        <option value="{{ $type }}" @selected(old('field_type', 'text') === $type)>
                            {{ __("clients.field_type_{$type}") }}
                        </option>
                    @endforeach
                </select>
                @error('field_type')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">{{ __('clients.field_sort_order') }}</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" dir="ltr"
                       class="form-input @error('sort_order') is-invalid @enderror">
                @error('sort_order')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="px-6 pb-5">
            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-plus"></i>
                {{ __('clients.add_extra_field') }}
            </button>
        </div>
    </form>
</div>

<div class="card overflow-hidden">
    @if($fields->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-sliders text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('clients.no_extra_fields') }}</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('clients.field_label_ar') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden md:table-cell">{{ __('clients.field_label_en') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('clients.field_type') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden sm:table-cell">{{ __('clients.field_sort_order') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.status') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($fields as $field)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-4 font-semibold text-slate-800">{{ $field->label_ar }}</td>
                        <td class="px-5 py-4 text-slate-500 hidden md:table-cell" dir="ltr">{{ $field->label_en ?: '—' }}</td>
                        <td class="px-5 py-4">
                            <span class="badge bg-indigo-100 text-indigo-700">{{ __("clients.field_type_{$field->field_type}") }}</span>
                        </td>
                        <td class="px-5 py-4 text-slate-500 hidden sm:table-cell">{{ $field->sort_order }}</td>
                        <td class="px-5 py-4">
                            @if($field->status)
                                <span class="badge bg-emerald-100 text-emerald-700">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                    {{ __('app.active') }}
                                </span>
                            @else
                                <span class="badge bg-slate-100 text-slate-500">
                                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span>
                                    {{ __('app.inactive') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('client-extra-fields.edit', $field) }}"
                                   class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="{{ __('app.edit') }}">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                <button type="button" title="{{ __('app.delete') }}"
                                        @click="$dispatch('delete-confirm', {
                                            action: '{{ route('client-extra-fields.destroy', $field) }}',
                                            message: '{{ __('app.delete_confirm_msg') }}'
                                        })"
                                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
