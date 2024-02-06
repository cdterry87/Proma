@props(['id', 'label', 'icon' => null])

<label
    for="{{ $id }}"
    class="btn btn-sm btn-primary flex items-center gap-1"
>
    @if ($icon)
        <x-dynamic-component :component="'icons.' . $icon" />
    @endif
    {{ $label ?? $slot }}
</label>
