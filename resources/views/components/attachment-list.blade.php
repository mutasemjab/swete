{{--
    @include('components.attachment-list', [
        'attachments' => $collection,      // each needs ->url and ->original_name (or ->name)
        'emptyText'   => __('...'),
        'canDelete'   => true,             // optional, defaults to true
        'destroyUrl'  => fn ($attachment) => route('...', [...]),
    ])
--}}
@forelse($attachments as $attachment)
    <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
        <a href="{{ $attachment->url }}" target="_blank" rel="noopener" class="text-indigo-600 hover:underline text-sm flex items-center gap-2">
            <i class="fa-solid fa-paperclip"></i>
            {{ $attachment->original_name ?? $attachment->name }}
        </a>
        @if($canDelete ?? true)
        <form action="{{ $destroyUrl($attachment) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                <i class="fa-solid fa-trash text-sm"></i>
            </button>
        </form>
        @endif
    </div>
@empty
    <p class="text-sm text-slate-400 mb-4">{{ $emptyText }}</p>
@endforelse
