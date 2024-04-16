@props([
    'label',
    'name',
    'id' => null,
    'placeholder' => null,
    'value',
    'hideLabel' => false,
    'rows' => 4,
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
    <textarea
        {{ $attributes->merge([
            'name' => $name,
            'id' => $id ?? $name,
            'placeholder' => $placeholder,
            'class' => 'textarea textarea-bordered w-full text-base ' . ($errors->has($name) ? 'textarea-error' : ''),
            'rows' => $rows,
        ]) }}
    >{{ $value ?? $slot }}</textarea>
    @error($name)
        <x-inputs.error :message="$message" />
    @enderror
</label>
