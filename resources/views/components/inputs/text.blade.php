@props([
    'label',
    'name',
    'id' => null,
    'placeholder' => null,
    'type' => 'text',
    'hideLabel' => false,
    'required' => false,
])

@php
    $label = $required ? "{$label} *" : "{$label} (Optional)";
@endphp

<label
    class="form-control w-full"
    aria-label="{{ $label }}"
>
    @if (!$hideLabel)
        <x-inputs.label :label="$label" />
    @endif
    <input
        {{ $attributes->merge([
            'name' => $name,
            'id' => $id ?? $name,
            'placeholder' => $placeholder,
            'class' => 'input input-bordered w-full ' . ($errors->has($name) ? 'input-error' : ''),
            'type' => $type,
        ]) }}
    />
    @error($name)
        <x-inputs.error :message="$message" />
    @enderror
</label>
