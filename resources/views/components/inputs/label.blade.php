@props(['label'])

<div class="label">
    <span {{ $attributes->merge([
        'class' => 'label-text',
    ]) }}>
        {{ $label }}
    </span>
</div>
