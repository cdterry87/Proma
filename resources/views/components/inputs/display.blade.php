@props(['label', 'value'])

<label
    class="form-control w-full"
    aria-label="{{ $label }}"
>
    <x-inputs.label :label="$label" />
    <div {{ $attributes->merge([
        'class' => 'font-semibold w-full px-1 text-2xl',
    ]) }}>
        {!! $value !!}
    </div>
</label>
