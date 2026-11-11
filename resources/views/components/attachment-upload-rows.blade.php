{{--
    @include('components.attachment-upload-rows', [
        'action'      => route(...),
        'addLabel'    => __('...'),
        'uploadLabel' => __('...'),
        'fileLabel'   => __('...'),
        'nameLabel'   => null,   // optional — adds a text "name" field per row (e.g. Project attachments)
        'extraFields' => [],     // optional — ['kind' => 'documents'] etc, rendered as hidden inputs
    ])
    Mirrors the row-add/remove Alpine pattern already used by Project/TenderAnalysis attachments.
--}}
<form action="{{ $action }}" method="POST" enctype="multipart/form-data"
      class="mt-4 pt-4 border-t border-slate-100 space-y-3"
      x-data="{
        nextId: 1,
        rows: [{ id: 0 }],
        addRow() { this.rows.push({ id: this.nextId++ }); },
        removeRow(id) { if (this.rows.length > 1) this.rows = this.rows.filter(r => r.id !== id); },
      }">
    @csrf
    @foreach($extraFields ?? [] as $key => $value)
        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
    @endforeach
    <template x-for="row in rows" :key="row.id">
        <div class="flex items-end gap-3 flex-wrap">
            @if($nameLabel ?? null)
            <div class="flex-1 min-w-40">
                <label class="form-label">{{ $nameLabel }}</label>
                <input type="text" :name="`attachments[${row.id}][name]`" class="form-input">
            </div>
            @endif
            <div class="flex-1 min-w-56">
                <label class="form-label">{{ $fileLabel }}</label>
                <input type="file" :name="`attachments[${row.id}][file]`" class="form-input">
            </div>
            <button type="button" @click="removeRow(row.id)"
                    class="p-2.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all flex-shrink-0">
                <i class="fa-solid fa-trash text-sm"></i>
            </button>
        </div>
    </template>
    <div class="flex items-center gap-3">
        <button type="button" @click="addRow()" class="btn-secondary btn-sm">
            <i class="fa-solid fa-plus"></i>
            {{ $addLabel }}
        </button>
        <button type="submit" class="btn-primary btn-sm">
            <i class="fa-solid fa-upload"></i>
            {{ $uploadLabel }}
        </button>
    </div>
</form>
