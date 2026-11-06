@props(['id', 'name'])

@error($name)
    <p id="{{ $id }}-error" class="field__error">{{ $message }}</p>
@enderror
