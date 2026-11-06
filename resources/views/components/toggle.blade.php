@props(['name', 'label'])

<div class="field">
    <label class="choice">
        <input type="checkbox" role="switch" name="{{ $name }}" {{ $attributes->class(['choice__input', 'visually-hidden']) }}>
        <span class="toggle" aria-hidden="true"></span>
        <span>{{ $label }}</span>
    </label>
    <x-field-error :id="$attributes->get('id', $name)" :name="$name" />
</div>
