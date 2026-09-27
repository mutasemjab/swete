@extends('layouts.app')

@section('title', __('maintenance.add_contract'))
@section('breadcrumb', __('maintenance.add_contract'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('maintenance.add_contract') }}</h1>
        <p class="page-subtitle">{{ __('maintenance.add_contract_subtitle') }}</p>
    </div>
    <a href="{{ route('maintenance-contracts.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('maintenance-contracts.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="card mb-5">
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="form-label">{{ __('maintenance.contract_customer') }} <span class="text-rose-500">*</span></label>
                <select name="customer_id" class="js-select2 form-select @error('customer_id') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->localized_name }} ({{ $customer->code }})</option>
                    @endforeach
                </select>
                @error('customer_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('maintenance.contract_file') }} <span class="text-rose-500">*</span></label>
                <input type="file" name="file" accept="application/pdf" class="form-input @error('file') is-invalid @enderror">
                <p class="text-xs text-slate-400 mt-1">{{ __('maintenance.contract_file_hint') }}</p>
                @error('file')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('maintenance.contract_signed_date') }} <span class="text-rose-500">*</span></label>
                <input type="date" name="signed_date" value="{{ old('signed_date', now()->toDateString()) }}"
                       class="form-input @error('signed_date') is-invalid @enderror">
                @error('signed_date')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('maintenance.contract_expiry_date') }} <span class="text-rose-500">*</span></label>
                <input type="date" name="expiry_date" value="{{ old('expiry_date') }}"
                       class="form-input @error('expiry_date') is-invalid @enderror">
                @error('expiry_date')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div class="sm:col-span-2">
                <label class="form-label">{{ __('maintenance.contract_notes') }}</label>
                <textarea name="notes" rows="2" class="form-input @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                @error('notes')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('maintenance.add_contract') }}
        </button>
        <a href="{{ route('maintenance-contracts.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
