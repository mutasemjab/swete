@php $appointment = $appointment ?? null; @endphp
<div class="card mb-5" x-data x-init="$nextTick(() => window.initSelect2())">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-calendar-days text-rose-500 text-sm"></i>
            {{ __('crm.appointments') }}
        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">

        <div class="sm:col-span-2">
            <label class="form-label">{{ __('crm.appointment_title') }} <span class="text-rose-500">*</span></label>
            <input type="text" name="title" value="{{ old('title', $appointment?->title) }}"
                   class="form-input @error('title') is-invalid @enderror">
            @error('title')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('crm.appointment_date') }} <span class="text-rose-500">*</span></label>
            <input type="date" name="appointment_date"
                   value="{{ old('appointment_date', $appointment?->appointment_date?->toDateString() ?? now()->toDateString()) }}"
                   class="form-input @error('appointment_date') is-invalid @enderror">
            @error('appointment_date')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('crm.appointment_type') }} <span class="text-rose-500">*</span></label>
            <select name="appointment_type_id" class="js-select2 form-select @error('appointment_type_id') is-invalid @enderror">
                <option value="">{{ __('app.select') }}</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}" @selected(old('appointment_type_id', $appointment?->appointment_type_id) == $type->id)>{{ $type->localized_name }}</option>
                @endforeach
            </select>
            @error('appointment_type_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('crm.appointment_assigned_to') }} <span class="text-rose-500">*</span></label>
            <select name="assigned_to" class="js-select2 form-select @error('assigned_to') is-invalid @enderror">
                <option value="">{{ __('app.select') }}</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" @selected(old('assigned_to', $appointment?->assigned_to ?? Auth::id()) == $employee->id)>{{ $employee->name }}</option>
                @endforeach
            </select>
            @error('assigned_to')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('crm.appointment_customer') }}</label>
            <select name="customer_id" class="js-select2 form-select @error('customer_id') is-invalid @enderror">
                <option value="">{{ __('app.select') }}</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" @selected(old('customer_id', $appointment?->customer_id) == $customer->id)>{{ $customer->localized_name }} ({{ $customer->code }})</option>
                @endforeach
            </select>
            @error('customer_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
            <label class="form-label">{{ __('crm.appointment_notes') }}</label>
            <textarea name="notes" rows="3" class="form-input @error('notes') is-invalid @enderror">{{ old('notes', $appointment?->notes) }}</textarea>
            @error('notes')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>
    </div>
</div>
