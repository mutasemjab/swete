{{--
    Reusable "quick add a material" modal — for the Price Quote and CIAT Discount screens, where a
    material referenced before its tender is actually won shouldn't yet exist in the real Warehouse
    catalog. Creates the material via AJAX as a DRAFT (Warehouse\MaterialController@quickStore,
    always is_draft=true), then injects the new option into every matching <select> on the page
    (there can be several material pickers at once — one per Price Quote item row) without leaving
    the current page. A draft material is promoted to a real one automatically once its tender
    converts to a project — see Tender::convertToProject().

    Props:
    - $targetSelector  CSS selector matching every <select> to inject the new option into,
                        e.g. 'select[name$="[material_id]"]' or '#ciat_material_id'
    - $categories, $units  for the minimal required fields
    - $label           button/modal label, e.g. __('warehouse.add_material_quick')
--}}
<div x-data="{
        open: false,
        saving: false,
        error: '',
        name: '',
        categoryId: '',
        unitId: '',
        submit() {
            this.saving = true;
            this.error = '';
            fetch('{{ route('warehouse.materials.quick-store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                },
                body: JSON.stringify({
                    name: this.name,
                    category_id: this.categoryId,
                    unit_id: this.unitId,
                }),
            })
                .then(async (res) => {
                    if (!res.ok) throw await res.json();
                    return res.json();
                })
                .then((data) => {
                    document.querySelectorAll('{{ $targetSelector }}').forEach((select) => {
                        const option = new Option(data.name + ' (' + data.code + ')', data.id);
                        select.appendChild(option);
                        $(select).trigger('change.select2');
                    });
                    this.open = false;
                    this.name = '';
                    this.categoryId = '';
                    this.unitId = '';
                })
                .catch((err) => { this.error = err.message || '{{ __('app.error_occurred') }}'; })
                .finally(() => { this.saving = false; });
        },
     }"
     class="inline-block">
    <button type="button" @click="open = true" class="btn-secondary btn-sm">
        <i class="fa-solid fa-plus"></i>
        {{ $label }}
    </button>

    <div x-show="open" x-cloak
         class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
         @keydown.escape.window="open = false">
        <div @click.outside="open = false" class="bg-white rounded-3xl shadow-2xl shadow-slate-900/20 p-6 max-w-sm w-full">
            <h3 class="text-lg font-black text-slate-800 mb-1.5">{{ $label }}</h3>
            <p class="text-xs text-slate-400 mb-4">{{ __('warehouse.add_material_quick_hint') }}</p>

            <label class="form-label">{{ __('warehouse.material_name') }}</label>
            <input type="text" x-model="name" @keydown.enter.prevent="submit()" class="form-input mb-3">

            <label class="form-label">{{ __('warehouse.material_category') }}</label>
            <select x-model="categoryId" class="form-select mb-3">
                <option value="">{{ __('app.select') }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->path }}</option>
                @endforeach
            </select>

            <label class="form-label">{{ __('warehouse.material_unit') }}</label>
            <select x-model="unitId" class="form-select">
                <option value="">{{ __('app.select') }}</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}">{{ $unit->localized_name }}</option>
                @endforeach
            </select>

            <p class="form-error" x-show="error" x-text="error"></p>
            <div class="flex gap-3 mt-5">
                <button type="button" @click="open = false" class="btn-secondary flex-1">{{ __('app.cancel') }}</button>
                <button type="button" :disabled="saving || !name || !categoryId || !unitId" @click="submit()" class="btn-primary flex-1">
                    <span x-show="!saving">{{ __('app.save') }}</span>
                    <span x-show="saving">…</span>
                </button>
            </div>
        </div>
    </div>
</div>
