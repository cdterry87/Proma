@props(['label', 'value'])

<label
    class="form-control w-full"
    aria-label="{{ $label }}"
>
    <x-inputs.label :label="$label" />
    <div>
        <p class="textarea textarea-bordered w-full flex items-center text-gray-400">{!! $value !!}</p>
    </div>
</label>
