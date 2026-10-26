@php $reminder = $reminder ?? null; @endphp
<div x-data="{
        items: {{ (
            $reminder?->items->map(fn ($i) => [
                'material_id' => $i->material_id,
                'quantity'    => (float) $i->quantity,
                'ercd'        => $i->ercd ?? '',
                'unit_price'  => (float) ($i->unit_price ?? 0),
                'features'    => $i->features->pluck('value')->values()->isNotEmpty() ? $i->features->pluck('value')->values() : [''],
            ])->values()
            ?? collect([['material_id' => '', 'quantity' => '', 'ercd' => '', 'unit_price' => '', 'features' => ['']]])
        )->toJson() }},
        addItem() { this.items.push({ material_id: '', quantity: '', ercd: '', unit_price: '', features: [''] }); this.$nextTick(() => window.initSelect2()); },
        removeItem(i) { if (this.items.length > 1) this.items.splice(i, 1); },
        projectId: '{{ old('project_id', $reminder?->project_id) }}',
        projectItems: {{ $projectItems->toJson() }},
        showProjectItemsPicker: false,
        get selectedProjectItems() { return this.projectItems[this.projectId] || []; },
        addFromProject(projectItem) {
            const newItem = { material_id: projectItem.material_id, quantity: projectItem.quantity, ercd: '', unit_price: projectItem.unit_price, features: [''] };
            if (this.items.length === 1 && !this.items[0].material_id) {
                this.items[0] = newItem;
            } else {
                this.items.push(newItem);
            }
            this.$nextTick(() => window.initSelect2());
        },
      }"
      x-init="$nextTick(() => window.initSelect2())">

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-bell text-orange-500 text-sm"></i>
                {{ __('tenders.reminder') }}
            </h3>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="form-label">{{ __('tenders.project') }} <span class="text-rose-500">*</span></label>
                <select name="project_id" x-model="projectId" class="js-select2 form-select @error('project_id') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" @selected(old('project_id', $reminder?->project_id) == $project->id)>{{ $project->number }} — {{ $project->localized_title }}</option>
                    @endforeach
                </select>
                @error('project_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('tenders.reminder_drive_url') }} <span class="text-rose-500">*</span></label>
                <input type="text" name="google_drive_url" value="{{ old('google_drive_url', $reminder?->google_drive_url) }}" dir="ltr"
                       placeholder="https://drive.google.com/..."
                       class="form-input @error('google_drive_url') is-invalid @enderror">
                @error('google_drive_url')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-list text-orange-500 text-sm"></i>
                {{ __('tenders.reminder_items') }}
            </h3>
            <div class="flex items-center gap-2">
                <button type="button" @click="showProjectItemsPicker = true" x-show="projectId" x-cloak class="btn-secondary btn-sm">
                    <i class="fa-solid fa-diagram-project"></i>
                    {{ __('tenders.reminder_choose_from_project') }}
                </button>
                <button type="button" @click="addItem()" class="btn-secondary btn-sm">
                    <i class="fa-solid fa-plus"></i>
                    {{ __('accounting.invoice_add_item') }}
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.material') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-28">{{ __('warehouse.voucher_item_quantity') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-28">{{ __('external_purchases.item_ercd') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-32">{{ __('accounting.invoice_item_unit_price') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-56">{{ __('external_purchases.item_features') }}</th>
                        <th class="px-5 py-3 w-10"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="(item, index) in items" :key="index">
                        <tr>
                            <td class="px-5 py-2.5">
                                <select :name="`items[${index}][material_id]`" x-model="item.material_id" class="js-select2 form-select" required>
                                    <option value="">{{ __('app.select') }}</option>
                                    @foreach($materials as $material)
                                        <option value="{{ $material->id }}">{{ $material->localized_name }} ({{ $material->code }})</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-5 py-2.5">
                                <input type="number" :name="`items[${index}][quantity]`" x-model="item.quantity"
                                       step="0.001" min="0.001" dir="ltr" class="form-input" required>
                            </td>
                            <td class="px-5 py-2.5">
                                <input type="text" :name="`items[${index}][ercd]`" x-model="item.ercd" dir="ltr" class="form-input">
                            </td>
                            <td class="px-5 py-2.5">
                                <input type="number" :name="`items[${index}][unit_price]`" x-model="item.unit_price"
                                       step="0.001" min="0" dir="ltr" class="form-input">
                            </td>
                            <td class="px-5 py-2.5">
                                <div class="space-y-1">
                                    <template x-for="(feature, fIndex) in item.features" :key="fIndex">
                                        <div class="flex items-center gap-1">
                                            <input type="text" :name="`items[${index}][features][${fIndex}]`" x-model="item.features[fIndex]"
                                                   class="form-input !py-1 !text-xs" placeholder="{{ __('external_purchases.item_feature_placeholder') }}">
                                            <button type="button" @click="item.features.splice(fIndex, 1)"
                                                    class="p-1 text-slate-300 hover:text-rose-600 flex-shrink-0">
                                                <i class="fa-solid fa-xmark text-xs"></i>
                                            </button>
                                        </div>
                                    </template>
                                    <button type="button" @click="item.features.push('')"
                                            class="text-xs font-semibold text-indigo-600 hover:underline">
                                        <i class="fa-solid fa-plus"></i> {{ __('external_purchases.add_feature') }}
                                    </button>
                                </div>
                            </td>
                            <td class="px-5 py-2.5 text-center">
                                <button type="button" @click="removeItem(index)" title="{{ __('accounting.invoice_remove_item') }}"
                                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        @error('items')<p class="form-error px-5 py-3"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
    </div>

    {{-- "Choose from this project's own items" modal — adds a reminder-item row pre-filled from
         one of the selected project's already-priced quote items, instead of re-typing materials
         the project already has. --}}
    <div x-show="showProjectItemsPicker" x-cloak
         class="fixed inset-0 z-[9998] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div @click.outside="showProjectItemsPicker = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[85vh] flex flex-col">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-800">{{ __('tenders.reminder_choose_from_project') }}</h3>
                <button type="button" @click="showProjectItemsPicker = false" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="px-6 py-4 overflow-y-auto flex-1">
                <template x-if="selectedProjectItems.length === 0">
                    <p class="text-sm text-slate-400 text-center py-6">{{ __('tenders.reminder_project_no_items') }}</p>
                </template>
                <div class="space-y-2">
                    <template x-for="(projectItem, pIndex) in selectedProjectItems" :key="pIndex">
                        <div class="flex items-center justify-between gap-3 border border-slate-200 rounded-xl px-4 py-2.5">
                            <div>
                                <p class="font-bold text-slate-800 text-sm" x-text="projectItem.name"></p>
                                <p class="text-xs text-slate-400" dir="ltr" x-text="'Qty: ' + projectItem.quantity"></p>
                            </div>
                            <button type="button" @click="addFromProject(projectItem)" class="btn-secondary btn-sm flex-shrink-0">
                                <i class="fa-solid fa-plus"></i>
                                {{ __('app.add') }}
                            </button>
                        </div>
                    </template>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-end">
                <button type="button" @click="showProjectItemsPicker = false" class="btn-secondary">{{ __('app.close') }}</button>
            </div>
        </div>
    </div>
</div>
