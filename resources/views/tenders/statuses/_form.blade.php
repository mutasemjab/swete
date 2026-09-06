@php $tenderStatus = $tenderStatus ?? null; @endphp
<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-list-check text-orange-500 text-sm"></i>
            {{ __('tenders.status') }}
        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label class="form-label">{{ __('tenders.status_name') }} <span class="text-rose-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $tenderStatus?->name) }}"
                   class="form-input @error('name') is-invalid @enderror">
            @error('name')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('app.name_en') }}</label>
            <input type="text" name="name_en" value="{{ old('name_en', $tenderStatus?->name_en) }}" dir="ltr"
                   class="form-input @error('name_en') is-invalid @enderror">
            @error('name_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('tenders.status_code') }} <span class="text-rose-500">*</span></label>
            <input type="text" name="code" value="{{ old('code', $tenderStatus?->code) }}" dir="ltr"
                   {{ $tenderStatus ? 'readonly' : '' }}
                   class="form-input @error('code') is-invalid @enderror {{ $tenderStatus ? 'bg-slate-50 text-slate-400' : '' }}">
            @error('code')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('tenders.status_color') }}</label>
            <select name="color" class="form-select @error('color') is-invalid @enderror">
                @foreach($colors as $color)
                    <option value="{{ $color }}" @selected(old('color', $tenderStatus?->color ?? 'slate') === $color)>{{ $color }}</option>
                @endforeach
            </select>
            @error('color')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        @if($tenderStatus)
        <div class="flex items-center">
            <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                <input type="hidden" name="status" value="0">
                <input type="checkbox" name="status" value="1" class="sr-only peer" @checked(old('status', $tenderStatus->status ?? true))>
                <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-indigo-400 rounded-full peer
                            peer-checked:bg-indigo-600 transition-all
                            after:content-[''] after:absolute after:top-0.5 after:start-[2px]
                            after:bg-white after:rounded-full after:h-5 after:w-5
                            after:transition-all peer-checked:after:translate-x-full"></div>
                <span class="ms-3 text-sm font-semibold text-slate-700">{{ __('app.active') }}</span>
            </label>
        </div>
        @endif
    </div>
</div>
