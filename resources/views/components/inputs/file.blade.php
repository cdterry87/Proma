@props(['label', 'name', 'id' => null, 'placeholder' => null, 'hideLabel' => false, 'multiple' => false])

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
            'class' => 'file-input file-input-bordered w-full',
            'type' => 'file',
            'multiple' => $multiple,
        ]) }}
    />
    @error($name)
        <x-inputs.error :message="$message" />
    @enderror
</label>
