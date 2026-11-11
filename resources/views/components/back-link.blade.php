{{-- @include('components.back-link', ['url' => $backUrl, 'label' => optional, 'class' => optional]) --}}
<a href="{{ $url }}" class="{{ $class ?? 'btn-secondary' }}">
    <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
    {{ $label ?? __('app.back_to_list') }}
</a>
