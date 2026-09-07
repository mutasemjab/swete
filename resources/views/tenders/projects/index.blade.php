@extends('layouts.app')

@section('title', __('tenders.projects_list'))
@section('breadcrumb', __('tenders.projects'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('tenders.projects_list') }}</h1>
        <p class="page-subtitle">{{ __('tenders.projects_subtitle') }}</p>
    </div>
</div>

<form method="GET" action="{{ route('projects.index') }}" class="card px-5 py-4 mb-4 flex flex-wrap gap-3">
    <div class="flex-1 min-w-48">
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute start-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="{{ __('app.search') }}..."
                   class="form-input ps-10">
        </div>
    </div>
    <button type="submit" class="btn-primary">
        <i class="fa-solid fa-filter"></i>
        {{ __('app.search') }}
    </button>
    @if(request()->hasAny(['search']))
        <a href="{{ route('projects.index') }}" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            {{ __('app.clear_filters') }}
        </a>
    @endif
</form>

<div class="card overflow-hidden">
    @if($projects->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-diagram-project text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('tenders.no_projects') }}</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.project_number') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.project_title') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden lg:table-cell">{{ __('tenders.project_customer') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden md:table-cell">{{ __('tenders.project_tender') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.project_status') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($projects as $project)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-4">
                            <a href="{{ route('projects.show', $project) }}" class="font-mono font-bold text-slate-700 hover:text-indigo-600 transition-colors bg-slate-100 px-2 py-1 rounded-lg text-sm">{{ $project->number }}</a>
                        </td>
                        <td class="px-5 py-4"><p class="font-bold text-slate-800">{{ $project->localized_title }}</p></td>
                        <td class="px-5 py-4 hidden lg:table-cell text-sm text-slate-600">{{ $project->customer?->localized_name ?? '—' }}</td>
                        <td class="px-5 py-4 hidden md:table-cell text-sm text-slate-600">
                            @if($project->tender)
                                <a href="{{ route('tenders.show', $project->tender) }}" class="text-indigo-600 hover:underline">{{ $project->tender->number }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <span class="badge bg-emerald-100 text-emerald-700">{{ __('tenders.project_status_' . $project->status) }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('projects.show', $project) }}"
                                   class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="{{ __('app.view') }}">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('projects.edit', $project) }}"
                                   class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="{{ __('app.edit') }}">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($projects->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                {{ __('app.showing') }} <span class="font-bold text-slate-700">{{ $projects->firstItem() }}</span>
                {{ __('app.to') }} <span class="font-bold text-slate-700">{{ $projects->lastItem() }}</span>
                {{ __('app.of') }} <span class="font-bold text-slate-700">{{ $projects->total() }}</span>
                {{ __('app.results') }}
            </p>
            <div class="flex gap-1">
                @if($projects->onFirstPage())
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.previous') }}</span>
                @else
                    <a href="{{ $projects->previousPageUrl() }}" class="btn-secondary btn-sm">{{ __('app.previous') }}</a>
                @endif
                @if($projects->hasMorePages())
                    <a href="{{ $projects->nextPageUrl() }}" class="btn-primary btn-sm">{{ __('app.next') }}</a>
                @else
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.next') }}</span>
                @endif
            </div>
        </div>
        @endif
    @endif
</div>
@endsection
