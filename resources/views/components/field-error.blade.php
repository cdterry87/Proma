@props(['id', 'name'])

@error($name)
    <p id="{{ $id }}-error" class="field__error" role="alert">{{ $message }}</p>
@enderror
