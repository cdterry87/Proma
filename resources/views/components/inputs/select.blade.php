@props(['label', 'name', 'id' => null, 'hideLabel' => false, 'defaultOption' => null, 'options' => null])

<label
    class="form-control w-full"
    aria-label="{{ $label }}"
>
    @if (!$hideLabel)
        <x-inputs.label :label="$label" />
    @endif
    <select
        {{ $attributes->merge([
            'name' => $name,
            'id' => $id ?? $name,
            'class' => 'select select-bordered w-full',
        ]) }}
    >
        @if ($defaultOption)
            <option
                disabled
                value=""
            >
                {{ $defaultOption }}
            </option>
        @endif
        @if ($options)
            @foreach ($options as $option)
                <option value="{{ $option->id }}">{{ $option->name }}</option>
            @endforeach
        @else
            {{ $slot }}
        @endif
    </select>
    @error($name)
        <x-inputs.error :message="$message" />
    @enderror
</label>
