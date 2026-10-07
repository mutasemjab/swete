@php $devicePassword = $devicePassword ?? null; @endphp
<div class="card mb-5" x-data="{ show: false }">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-key text-orange-500 text-sm"></i>
            {{ __('maintenance.device_password') }}
        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label class="form-label">{{ __('maintenance.device_name') }} <span class="text-rose-500">*</span></label>
            <input type="text" name="device_name" value="{{ old('device_name', $devicePassword?->device_name) }}"
                   class="form-input @error('device_name') is-invalid @enderror">
            @error('device_name')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('app.password') }} <span class="text-rose-500">*</span></label>
            <div class="relative">
                <input :type="show ? 'text' : 'password'" name="password" value="{{ old('password', $devicePassword?->password) }}" dir="ltr"
                       class="form-input pe-10 @error('password') is-invalid @enderror">
                <button type="button" @click="show = !show"
                        class="absolute end-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 transition-colors">
                    <i :class="show ? 'fa-eye-slash' : 'fa-eye'" class="fa-solid text-sm"></i>
                </button>
            </div>
            @error('password')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
            <label class="form-label">{{ __('app.notes') }}</label>
            <textarea name="notes" rows="2" class="form-input @error('notes') is-invalid @enderror">{{ old('notes', $devicePassword?->notes) }}</textarea>
            @error('notes')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>
    </div>
</div>
