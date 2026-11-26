@extends('layouts.app')

@section('title', __('equipment.documents_list'))
@section('breadcrumb', __('equipment.documents_list'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('equipment.documents_list') }}</h1>
        <p class="page-subtitle">{{ __('equipment.documents_subtitle') }}</p>
    </div>
    @can('equipment.documents.upload')
    <a href="{{ route('documents.create') }}" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        {{ __('equipment.add_document') }}
    </a>
    @endcan
</div>

<form method="GET" action="{{ route('documents.index') }}" class="card px-5 py-4 mb-4 flex flex-wrap gap-3">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('equipment.document_search_placeholder') }}"
           class="form-input w-56">
    <select name="document_type_id" class="js-select2 form-select w-48">
        <option value="">{{ __('equipment.all_document_types') }}</option>
        @foreach($types as $type)
            <option value="{{ $type->id }}" @selected(request('document_type_id') == $type->id)>{{ $type->localized_name }}</option>
        @endforeach
    </select>
    <label class="flex items-center gap-2 text-sm text-slate-600 px-2">
        <input type="checkbox" name="show_archived" value="1" @checked(request()->boolean('show_archived'))>
        {{ __('equipment.show_archived') }}
    </label>
    <button type="submit" class="btn-primary">
        <i class="fa-solid fa-filter"></i>
        {{ __('app.search') }}
    </button>
    @if(request()->hasAny(['search', 'document_type_id', 'show_archived']))
        <a href="{{ route('documents.index') }}" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            {{ __('app.clear_filters') }}
        </a>
    @endif
</form>

<div class="card overflow-hidden">
    @if($documents->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-file-lines text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('equipment.no_documents') }}</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('equipment.document_name') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('equipment.document_type') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('equipment.document_scope') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($documents as $document)
                    <tr class="hover:bg-slate-50/50 transition-colors group {{ $document->is_archived ? 'opacity-50' : '' }}">
                        <td class="px-5 py-4">
                            <a href="{{ $document->url }}" target="_blank" class="font-bold text-slate-800 hover:text-indigo-600 transition-colors">{{ $document->localized_name }}</a>
                            @if($document->revision)<p class="text-xs text-slate-400">{{ __('equipment.document_revision') }}: {{ $document->revision }}</p>@endif
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $document->type?->localized_name }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">
                            {{ $document->assignment?->scopeLabel() }}
                            @if($document->assignments_count > 1)<span class="badge bg-cyan-100 text-cyan-800">{{ __('maintenance.import_assignment_count', ['count' => $document->assignments_count]) }}</span>@endif
                            @if($document->is_archived)<span class="badge bg-slate-100 text-slate-500 ms-1">{{ __('equipment.archived') }}</span>@endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                @can('equipment.documents.edit')
                                <a href="{{ route('documents.edit', $document) }}"
                                   class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="{{ __('app.edit') }}">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                <form action="{{ route('documents.archive', $document) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" title="{{ $document->is_archived ? __('equipment.unarchive') : __('equipment.archive') }}"
                                            class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all">
                                        <i class="fa-solid fa-box-archive text-sm"></i>
                                    </button>
                                </form>
                                @endcan
                                @can('equipment.documents.delete')
                                <button type="button" title="{{ __('app.delete') }}"
                                        @click="$dispatch('delete-confirm', { action: '{{ route('documents.destroy', $document) }}', message: '{{ __('app.delete_confirm_msg') }}' })"
                                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($documents->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                {{ __('app.showing') }} <span class="font-bold text-slate-700">{{ $documents->firstItem() }}</span>
                {{ __('app.to') }} <span class="font-bold text-slate-700">{{ $documents->lastItem() }}</span>
                {{ __('app.of') }} <span class="font-bold text-slate-700">{{ $documents->total() }}</span>
                {{ __('app.results') }}
            </p>
            <div class="flex gap-1">
                @if($documents->onFirstPage())
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.previous') }}</span>
                @else
                    <a href="{{ $documents->previousPageUrl() }}" class="btn-secondary btn-sm">{{ __('app.previous') }}</a>
                @endif
                @if($documents->hasMorePages())
                    <a href="{{ $documents->nextPageUrl() }}" class="btn-primary btn-sm">{{ __('app.next') }}</a>
                @else
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.next') }}</span>
                @endif
            </div>
        </div>
        @endif
    @endif
</div>
@endsection
