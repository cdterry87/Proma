@props(['label', 'value'])

<label
    class="form-control w-full"
    aria-label="{{ $label }}"
>
    <x-inputs.label :label="$label" />
    <div {{ $attributes->merge([
        'class' => 'font-bold w-full px-1',
    ]) }}>
        {!! $value !!}
    </div>
</label>
