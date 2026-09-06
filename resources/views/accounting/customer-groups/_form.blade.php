@php $group = $group ?? null; @endphp
<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-sitemap text-blue-500 text-sm"></i>
            {{ __('accounting.group') }}
        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label class="form-label">{{ __('accounting.group_name') }} <span class="text-rose-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $group?->name) }}"
                   class="form-input @error('name') is-invalid @enderror">
            @error('name')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('app.name_en') }}</label>
            <input type="text" name="name_en" value="{{ old('name_en', $group?->name_en) }}" dir="ltr"
                   class="form-input @error('name_en') is-invalid @enderror">
            @error('name_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('accounting.parent_group') }}</label>
            <select name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                <option value="">{{ __('accounting.no_parent_group') }}</option>
                @foreach($parents as $parent)
                    <option value="{{ $parent->id }}" @selected(old('parent_id', $group?->parent_id) == $parent->id)>{{ $parent->localized_name }}</option>
                @endforeach
            </select>
            @error('parent_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center pt-1">
            <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                <input type="hidden" name="status" value="0">
                <input type="checkbox" name="status" value="1" class="sr-only peer"
                       @checked(old('status', $group?->status ?? true))>
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
