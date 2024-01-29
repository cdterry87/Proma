@props(['label', 'name', 'id' => null, 'hiddenLabel' => false])

<div class="form-control">
    <label
        class="label cursor-pointer"
        aria-label="{{ $label }}"
    >
        @if (!$hiddenLabel)
            <span class="label-text">{{ $label }}</span>
        @endif
        <input
            type="checkbox"
            {{ $attributes->merge([
                'name' => $name,
                'id' => $id ?? $name,
                'class' => 'checkbox',
            ]) }}
            checked="checked"
        />
    </label>
</div>
