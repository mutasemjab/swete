@php
    $schedule = $schedule ?? null;
    $manualDatesText = $schedule?->manual_dates ? implode("\n", $schedule->manual_dates) : '';
@endphp
<div class="card mb-5"
     x-data="{ scope: '{{ old('scope', $schedule?->scope ?? 'general') }}', mode: '{{ old('mode', $schedule?->mode ?? 'interval') }}' }"
     x-init="$nextTick(() => window.initSelect2())">
    <div class="card-header">
        <h3 class="font-bold text-slate-700">{{ __('maintenance.schedule') }}</h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label class="form-label">{{ __('maintenance.schedule_scope') }} <span class="text-rose-500">*</span></label>
            <select name="scope" x-model="scope"
                    @change="$nextTick(() => { $(document).find('.js-schedule-scope-select.select2-hidden-accessible').select2('destroy'); window.initSelect2(); })"
                    class="form-select @error('scope') is-invalid @enderror">
                <option value="general" @selected(old('scope', $schedule?->scope) == 'general')>{{ __('maintenance.schedule_scope_general') }}</option>
                <option value="unit" @selected(old('scope', $schedule?->scope) == 'unit')>{{ __('maintenance.schedule_scope_unit') }}</option>
            </select>
            @error('scope')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div x-show="scope === 'unit'">
            <label class="form-label">{{ __('equipment.unit') }}</label>
            <select name="equipment_unit_id" class="js-select2 js-schedule-scope-select form-select @error('equipment_unit_id') is-invalid @enderror">
                <option value="">{{ __('app.select') }}</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}" @selected(old('equipment_unit_id', $schedule?->equipment_unit_id) == $unit->id)>{{ $unit->localized_name }}</option>
                @endforeach
            </select>
            @error('equipment_unit_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div x-show="scope === 'general'" class="flex items-center">
            <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                <input type="hidden" name="include_new_units" value="0">
                <input type="checkbox" name="include_new_units" value="1" class="sr-only peer" @checked(old('include_new_units', $schedule?->include_new_units ?? true))>
                <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-indigo-400 rounded-full peer
                            peer-checked:bg-indigo-600 transition-all
                            after:content-[''] after:absolute after:top-0.5 after:start-[2px]
                            after:bg-white after:rounded-full after:h-5 after:w-5
                            after:transition-all peer-checked:after:translate-x-full"></div>
                <span class="ms-3 text-sm font-semibold text-slate-700">{{ __('maintenance.schedule_include_new_units') }}</span>
            </label>
        </div>

        <div>
            <label class="form-label">{{ __('maintenance.schedule_mode') }} <span class="text-rose-500">*</span></label>
            <select name="mode" x-model="mode" class="form-select @error('mode') is-invalid @enderror">
                <option value="interval" @selected(old('mode', $schedule?->mode) == 'interval')>{{ __('maintenance.schedule_mode_interval') }}</option>
                <option value="manual" @selected(old('mode', $schedule?->mode) == 'manual')>{{ __('maintenance.schedule_mode_manual') }}</option>
            </select>
            @error('mode')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div x-show="mode === 'interval'" class="flex gap-3">
            <div class="flex-1">
                <label class="form-label">{{ __('maintenance.schedule_interval_value') }}</label>
                <input type="number" name="interval_value" min="1" max="60" dir="ltr" value="{{ old('interval_value', $schedule?->interval_value ?? 3) }}" class="form-input @error('interval_value') is-invalid @enderror">
            </div>
            <div class="flex-1">
                <label class="form-label">{{ __('maintenance.schedule_interval_unit') }}</label>
                <select name="interval_unit" class="form-select @error('interval_unit') is-invalid @enderror">
                    <option value="day" @selected(old('interval_unit', $schedule?->interval_unit) == 'day')>{{ __('maintenance.schedule_interval_day') }}</option>
                    <option value="week" @selected(old('interval_unit', $schedule?->interval_unit) == 'week')>{{ __('maintenance.schedule_interval_week') }}</option>
                    <option value="month" @selected(old('interval_unit', $schedule?->interval_unit ?? 'month') == 'month')>{{ __('maintenance.schedule_interval_month') }}</option>
                </select>
            </div>
            @error('interval_value')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            @error('interval_unit')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div x-show="mode === 'manual'" class="sm:col-span-2">
            <label class="form-label">{{ __('maintenance.schedule_manual_dates') }}</label>
            <textarea name="manual_dates_text" rows="4" dir="ltr" placeholder="2026-01-15&#10;2026-04-15&#10;2026-07-15"
                      class="form-input @error('manual_dates_text') is-invalid @enderror">{{ old('manual_dates_text', $manualDatesText) }}</textarea>
            <p class="text-xs text-slate-400 mt-1">{{ __('maintenance.schedule_manual_dates_hint') }}</p>
            @error('manual_dates_text')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('maintenance.schedule_start_date') }} <span class="text-rose-500">*</span></label>
            <input type="date" name="start_date" value="{{ old('start_date', $schedule?->start_date?->toDateString() ?? $contract->signed_date->toDateString()) }}" class="form-input @error('start_date') is-invalid @enderror">
            @error('start_date')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('maintenance.schedule_end_date') }}</label>
            <input type="date" name="end_date" value="{{ old('end_date', $schedule?->end_date?->toDateString()) }}" class="form-input @error('end_date') is-invalid @enderror">
            <p class="text-xs text-slate-400 mt-1">{{ __('maintenance.schedule_end_date_hint') }}</p>
            @error('end_date')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('maintenance.schedule_engineer') }}</label>
            <select name="engineer_id" class="js-select2 form-select @error('engineer_id') is-invalid @enderror">
                <option value="">{{ __('app.none') }}</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected(old('engineer_id', $schedule?->engineer_id) == $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
            @error('engineer_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('maintenance.schedule_technician') }}</label>
            <select name="technician_id" class="js-select2 form-select @error('technician_id') is-invalid @enderror">
                <option value="">{{ __('app.none') }}</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected(old('technician_id', $schedule?->technician_id) == $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
            @error('technician_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
            <label class="form-label">{{ __('maintenance.payment_notes') }}</label>
            <textarea name="notes" rows="2" class="form-input @error('notes') is-invalid @enderror">{{ old('notes', $schedule?->notes) }}</textarea>
            @error('notes')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>
    </div>
</div>
