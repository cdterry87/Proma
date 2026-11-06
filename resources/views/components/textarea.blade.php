@props(['name', 'label', 'hint' => null, 'hideLabel' => false, 'required' => false])

@php
    $id = $attributes->get('id', $name);
    $invalid = $errors->has($name);
    $describedBy = collect([$hint ? $id.'-hint' : null, $invalid ? $id.'-error' : null])->filter()->implode(' ');
@endphp

<x-field :id="$id" :name="$name" :label="$label" :hint="$hint" :hide-label="$hideLabel" :required="$required">
    <textarea
        {{ $attributes->class('input')->merge([
            'id' => $id,
            'name' => $name,
            'required' => $required,
            'aria-invalid' => $invalid ? 'true' : null,
            'aria-describedby' => $describedBy ?: null,
        ]) }}
    >{{ $slot }}</textarea>
</x-field>
