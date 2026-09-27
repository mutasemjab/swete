@extends('layouts.app')

@section('title', __('maintenance.contract_payments'))
@section('breadcrumb', __('maintenance.contract_payments'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('maintenance.contract_payments') }}</h1>
        <p class="page-subtitle">{{ __('maintenance.contracts_subtitle') }}</p>
    </div>
</div>

<form method="GET" action="{{ route('contract-payments.index') }}" class="card px-5 py-4 mb-4 flex flex-wrap gap-3">
    <select name="assigned_to" class="js-select2 form-select w-56">
        <option value="">{{ __('maintenance.all_employees') }}</option>
        @foreach($employees as $employee)
            <option value="{{ $employee->id }}" @selected(request('assigned_to') == $employee->id)>{{ $employee->name }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn-primary">
        <i class="fa-solid fa-filter"></i>
        {{ __('app.search') }}
    </button>
    @if(request()->hasAny(['assigned_to']))
        <a href="{{ route('contract-payments.index') }}" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            {{ __('app.clear_filters') }}
        </a>
    @endif
</form>

<div class="card overflow-hidden" x-data="{
        selected: [],
        showAssignModal: false,
        toggleAll(checked) { this.selected = checked ? {{ $payments->getCollection()->whereNull('invoice_id')->pluck('id')->values()->toJson() }} : []; },
      }">
    @if($payments->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-money-check-dollar text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('maintenance.no_payments') }}</p>
        </div>
    @else
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between" x-show="selected.length > 0" x-cloak>
            <p class="text-sm font-semibold text-slate-600" x-text="selected.length + ' {{ __('app.selected') }}'"></p>
            <button type="button" @click="showAssignModal = true" class="btn-secondary btn-sm">
                <i class="fa-solid fa-paper-plane"></i>
                {{ __('maintenance.payment_send_to_employee') }}
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 w-10">
                            <input type="checkbox" @change="toggleAll($event.target.checked)" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        </th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('maintenance.contract_number') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('maintenance.contract_customer') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('maintenance.payment_due_date') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('maintenance.payment_amount') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('maintenance.payment_assigned_to') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($payments as $payment)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-4">
                            @if(! $payment->invoice_id)
                                <input type="checkbox" value="{{ $payment->id }}" x-model.number="selected" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <a href="{{ route('maintenance-contracts.show', $payment->contract) }}" class="font-mono font-bold text-slate-700 hover:text-indigo-600 transition-colors">{{ $payment->contract?->number }}</a>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $payment->contract?->customer?->localized_name }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600" dir="ltr">{{ $payment->due_date->format('Y-m-d') }}</td>
                        <td class="px-5 py-4 font-bold text-slate-800" dir="ltr">{{ number_format($payment->amount, 3) }} {{ $payment->currency?->code }}</td>
                        <td class="px-5 py-4">
                            @if($payment->invoice_id)
                                <span class="badge bg-emerald-100 text-emerald-700">{{ __('maintenance.payment_status_invoiced') }}</span>
                            @elseif($payment->assignee)
                                <span class="badge bg-violet-100 text-violet-700">{{ $payment->assignee->name }}</span>
                            @else
                                <span class="text-xs text-slate-400">{{ __('maintenance.payment_unassigned') }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5">
                                @if($payment->invoice_id)
                                    <a href="{{ route('accounting.invoices.show', $payment->invoice_id) }}"
                                       class="p-1.5 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all" title="{{ __('maintenance.payment_view_invoice') }}">
                                        <i class="fa-solid fa-file-invoice-dollar text-sm"></i>
                                    </a>
                                @elseif($payment->assigned_to === Auth::id())
                                    <form action="{{ route('contract-payments.convert-to-invoice', $payment) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn-secondary btn-sm" title="{{ __('maintenance.payment_convert_to_invoice') }}">
                                            <i class="fa-solid fa-file-invoice-dollar"></i>
                                            {{ __('maintenance.payment_convert_to_invoice') }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                {{ __('app.showing') }} <span class="font-bold text-slate-700">{{ $payments->firstItem() }}</span>
                {{ __('app.to') }} <span class="font-bold text-slate-700">{{ $payments->lastItem() }}</span>
                {{ __('app.of') }} <span class="font-bold text-slate-700">{{ $payments->total() }}</span>
                {{ __('app.results') }}
            </p>
            <div class="flex gap-1">
                @if($payments->onFirstPage())
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.previous') }}</span>
                @else
                    <a href="{{ $payments->previousPageUrl() }}" class="btn-secondary btn-sm">{{ __('app.previous') }}</a>
                @endif
                @if($payments->hasMorePages())
                    <a href="{{ $payments->nextPageUrl() }}" class="btn-primary btn-sm">{{ __('app.next') }}</a>
                @else
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.next') }}</span>
                @endif
            </div>
        </div>
        @endif

        {{-- Send-to-employee modal --}}
        <div x-show="showAssignModal" x-cloak
             class="fixed inset-0 z-[9998] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
            <div @click.outside="showAssignModal = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
                <form action="{{ route('contract-payments.assign') }}" method="POST">
                    @csrf
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="payment_ids[]" :value="id">
                    </template>
                    <div class="px-6 py-5 border-b border-slate-100">
                        <h3 class="font-bold text-slate-800">{{ __('maintenance.payment_send_to_employee') }}</h3>
                        <p class="text-xs text-slate-400 mt-1">{{ __('maintenance.payment_send_to_employee_hint') }}</p>
                    </div>
                    <div class="px-6 py-5">
                        <label class="form-label">{{ __('maintenance.payment_select_employee') }} <span class="text-rose-500">*</span></label>
                        <select name="assigned_to" class="form-select" required>
                            <option value="">{{ __('app.select') }}</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="showAssignModal = false" class="btn-secondary">{{ __('app.cancel') }}</button>
                        <button type="submit" class="btn-primary">
                            <i class="fa-solid fa-paper-plane"></i>
                            {{ __('maintenance.payment_send') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
