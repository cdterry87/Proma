@props(['label', 'name', 'id' => null, 'hiddenLabel' => false])

<div class="form-control">
    <label
        class="label cursor-pointer flex items-center gap-2"
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
                'class' => 'toggle toggle-success',
            ]) }}
            checked="checked"
        />
    </label>
</div>
