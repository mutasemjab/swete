@php $discount = $discount ?? null; @endphp
<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-percent text-orange-500 text-sm"></i>
            {{ __('tenders.ciat_discounts_list') }}
        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label class="form-label">{{ __('tenders.ciat_type') }} <span class="text-rose-500">*</span></label>
            <div class="flex items-start gap-2">
                <div class="flex-1">
                    <select id="ciat_material_id" name="material_id" class="js-select2 form-select @error('material_id') is-invalid @enderror">
                        <option value="">{{ __('app.select') }}</option>
                        @foreach($materials as $material)
                            <option value="{{ $material->id }}" @selected(old('material_id', $discount?->material_id) == $material->id)>{{ $material->localized_name }} ({{ $material->code }})</option>
                        @endforeach
                    </select>
                    @error('material_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                </div>
                @include('components.material-quick-add-modal', [
                    'targetSelector' => '#ciat_material_id',
                    'categories'     => $materialCategories,
                    'units'          => $units,
                    'label'          => __('warehouse.add_material_quick'),
                ])
            </div>
        </div>

        <div>
            <label class="form-label">{{ __('tenders.ciat_discount_percent') }} <span class="text-rose-500">*</span></label>
            <input type="number" step="0.01" min="0" max="100" name="discount_percent" value="{{ old('discount_percent', $discount?->discount_percent) }}" dir="ltr"
                   class="form-input @error('discount_percent') is-invalid @enderror">
            @error('discount_percent')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        @if($discount)
        <div class="flex items-center">
            <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                <input type="hidden" name="status" value="0">
                <input type="checkbox" name="status" value="1" class="sr-only peer" @checked(old('status', $discount->status ?? true))>
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
