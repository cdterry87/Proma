@props(['label', 'value'])

{{-- @todo - add a "show more" button when the value is over a certain length using line-clamp --}}

<label
    class="form-control w-full"
    aria-label="{{ $label }}"
>
    <x-inputs.label :label="$label" />
    <div {{ $attributes->merge([
        'class' => 'font-semibold w-full px-1 text-2xl line-clamp-4',
    ]) }}>
        {!! $value !!}
    </div>
</label>
