@props(['name', 'label'])

@php
    $id = $attributes->get('id', $name);
    $invalid = $errors->has($name);
@endphp

<div class="field">
    <label class="choice">
        <input
            type="checkbox"
            {{ $attributes->class('choice__input')->merge([
                'id' => $id,
                'name' => $name,
                'aria-invalid' => $invalid ? 'true' : null,
                'aria-describedby' => $invalid ? $id.'-error' : null,
            ]) }}
        >
        <span>{{ $label }}</span>
    </label>
    <x-field-error :id="$id" :name="$name" />
</div>
