@props(['name', 'label'])

<div class="field">
    <label class="choice">
        <input type="checkbox" name="{{ $name }}" {{ $attributes->class('choice__input') }}>
        <span>{{ $label }}</span>
    </label>
    <x-field-error :id="$attributes->get('id', $name)" :name="$name" />
</div>
