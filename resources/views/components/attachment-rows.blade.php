{{--
    @include('components.attachment-rows', [
        'fileLabel' => __('...'),
        'addLabel'  => __('...'),
        'required'  => true,   // optional — marks the first row's file input required
    ])
    Row-add/remove file-input rows meant to be embedded inside an ALREADY-open <form> (e.g. a
    create form where attachments are required alongside the rest of the fields) — unlike
    components.attachment-upload-rows, this renders no <form>/@csrf/submit button of its own.
--}}
<div x-data="{
       nextId: 1,
       rows: [{ id: 0 }],
       addRow() { this.rows.push({ id: this.nextId++ }); },
       removeRow(id) { if (this.rows.length > 1) this.rows = this.rows.filter(r => r.id !== id); },
     }">
    <template x-for="(row, index) in rows" :key="row.id">
        <div class="flex items-end gap-3 flex-wrap mb-3">
            <div class="flex-1 min-w-56">
                <label class="form-label">{{ $fileLabel }}</label>
                <input type="file" :name="`attachments[${row.id}][file]`" class="form-input" :required="index === 0 && {{ ($required ?? false) ? 'true' : 'false' }}">
            </div>
            <button type="button" @click="removeRow(row.id)"
                    class="p-2.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all flex-shrink-0">
                <i class="fa-solid fa-trash text-sm"></i>
            </button>
        </div>
    </template>
    <button type="button" @click="addRow()" class="btn-secondary btn-sm">
        <i class="fa-solid fa-plus"></i>
        {{ $addLabel }}
    </button>
</div>
