@php $warehouse = $warehouse ?? null; @endphp
<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-warehouse text-emerald-500 text-sm"></i>
            {{ __('warehouse.warehouse') }}
        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">

        <div>
            <label class="form-label">{{ __('warehouse.warehouse_name') }} <span class="text-rose-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $warehouse?->name) }}"
                   class="form-input @error('name') is-invalid @enderror">
            @error('name')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('app.name_en') }}</label>
            <input type="text" name="name_en" value="{{ old('name_en', $warehouse?->name_en) }}" dir="ltr"
                   class="form-input @error('name_en') is-invalid @enderror">
            @error('name_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('warehouse.warehouse_code') }} <span class="text-rose-500">*</span></label>
            <input type="text" name="code" value="{{ old('code', $warehouse?->code) }}" dir="ltr"
                   class="form-input @error('code') is-invalid @enderror">
            @error('code')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('warehouse.warehouse_branch') }} <span class="text-rose-500">*</span></label>
            <select name="branch_id" class="form-select @error('branch_id') is-invalid @enderror">
                <option value="">{{ __('app.select') }}</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" @selected(old('branch_id', $warehouse?->branch_id) == $branch->id)>{{ $branch->localized_name }}</option>
                @endforeach
            </select>
            @error('branch_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div class="flex flex-col gap-4 pt-2 sm:col-span-2">
            <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                <input type="hidden" name="is_main" value="0">
                <input type="checkbox" name="is_main" value="1" class="sr-only peer"
                       @checked(old('is_main', $warehouse?->is_main))>
                <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-emerald-400 rounded-full peer
                            peer-checked:bg-emerald-600 transition-all
                            after:content-[''] after:absolute after:top-0.5 after:start-[2px]
                            after:bg-white after:rounded-full after:h-5 after:w-5
                            after:transition-all peer-checked:after:translate-x-full"></div>
                <span class="ms-3 text-sm font-semibold text-slate-700">{{ __('warehouse.is_main_warehouse') }}</span>
            </label>

            <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                <input type="hidden" name="status" value="0">
                <input type="checkbox" name="status" value="1" class="sr-only peer"
                       @checked(old('status', $warehouse?->status ?? true))>
                <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-indigo-400 rounded-full peer
                            peer-checked:bg-indigo-600 transition-all
                            after:content-[''] after:absolute after:top-0.5 after:start-[2px]
                            after:bg-white after:rounded-full after:h-5 after:w-5
                            after:transition-all peer-checked:after:translate-x-full"></div>
                <span class="ms-3 text-sm font-semibold text-slate-700">{{ __('app.active') }}</span>
            </label>
        </div>
    </div>
</div>
