@props(['label', 'value'])

@php
    $isExpandable = strlen($value) > 100 ? true : false;
@endphp

<div
    class="flex flex-col gap-6"
    x-data="{ isExpanded: false }"
>
    <label
        class="form-control w-full"
        aria-label="{{ $label }}"
        aria-describedby="{{ $label }}"
    >
        <x-inputs.label :label="$label" />
        <div {{ $attributes->merge([
            'class' => 'font-bold w-full px-1 text-2xl',
        ]) }}>
            <p
                x-show="!isExpanded"
                class="whitespace-pre-line"
            >{!! Str::limit($value, 150) !!}</p>
            <p
                x-show="isExpanded"
                class="whitespace-pre-line"
            >{!! $value !!}</p>
        </div>
    </label>

    @if ($isExpandable)
        <div>
            <button
                type="button"
                class="text-accent font-bold hover:brightness-110 text-base"
                aria-label="Toggle expand"
                @click.prevent="isExpanded = !isExpanded"
            >
                <span
                    x-show="!isExpanded"
                    class="flex items-center gap-1"
                >
                    <x-icons.plus /> Show More
                </span>
                <span
                    x-show="isExpanded"
                    class="flex items-center gap-1"
                >
                    <x-icons.minus /> Show Less
                </span>
            </button>
        </div>
    @endif
</div>
