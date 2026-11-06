@props(['id', 'name', 'label', 'hint' => null, 'hideLabel' => false, 'required' => false])

<div class="field">
    <label for="{{ $id }}" @class(['field__label', 'visually-hidden' => $hideLabel])>
        {{ $label }}
        @if ($required)
            <span class="field__required" aria-hidden="true">*</span>
        @endif
    </label>

    {{ $slot }}

    @if ($hint)
        <p id="{{ $id }}-hint" class="field__hint">{{ $hint }}</p>
    @endif

    <x-field-error :id="$id" :name="$name" />
</div>
