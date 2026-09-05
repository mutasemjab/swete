@extends('layouts.app')

@section('title', __('accounting.invoice_types_list'))
@section('breadcrumb', __('accounting.invoice_types'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('accounting.invoice_types_list') }}</h1>
        <p class="page-subtitle">{{ __('accounting.invoice_types_subtitle') }}</p>
    </div>
    <a href="{{ route('accounting.invoice-types.create') }}" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        {{ __('accounting.add_invoice_type') }}
    </a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="card px-5 py-4 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-tags text-blue-600"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800">{{ $invoiceTypes->count() }}</p>
            <p class="text-xs text-slate-500 font-medium mt-0.5">{{ __('accounting.total_invoice_types') }}</p>
        </div>
    </div>
</div>

<div class="card overflow-hidden">
    @if($invoiceTypes->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-tags text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('accounting.no_invoice_types') }}</p>
            <a href="{{ route('accounting.invoice-types.create') }}" class="btn-primary mt-5">
                <i class="fa-solid fa-plus"></i>
                {{ __('accounting.add_first_invoice_type') }}
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('accounting.invoice_type_name') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('accounting.invoice_type_code') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('accounting.invoice_type_party_type') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.status') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($invoiceTypes as $invoiceType)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <p class="font-bold text-slate-800">{{ $invoiceType->localized_name }}</p>
                                @if($invoiceType->is_system)
                                    <span class="badge bg-blue-100 text-blue-700 text-[10px]">{{ __('accounting.is_system_type') }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="font-mono text-sm text-slate-600 bg-slate-100 px-2 py-1 rounded-lg">{{ $invoiceType->code }}</span>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ __('accounting.party_type_' . $invoiceType->party_type) }}</td>
                        <td class="px-5 py-4">
                            @if($invoiceType->status)
                                <span class="badge bg-emerald-100 text-emerald-700">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>{{ __('app.active') }}
                                </span>
                            @else
                                <span class="badge bg-slate-100 text-slate-500">
                                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span>{{ __('app.inactive') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('accounting.invoice-types.edit', $invoiceType) }}"
                                   class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="{{ __('app.edit') }}">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                @if(!$invoiceType->is_system)
                                <button type="button" title="{{ __('app.delete') }}"
                                        @click="$dispatch('delete-confirm', { action: '{{ route('accounting.invoice-types.destroy', $invoiceType) }}', message: '{{ __('app.delete_confirm_msg') }}' })"
                                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </button>
                                @else
                                <div class="p-1.5 text-slate-300 !cursor-not-allowed" title="{{ __('accounting.is_system_type_hint') }}">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </div>
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
@endsection
