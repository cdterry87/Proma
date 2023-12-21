@props(['label' => null])

<div>
    <label class="flex items-center cursor-pointer select-none text-gray-700 dark:text-white">
        @if ($label)
            <span class="pr-2">{{ $label }}</span>
        @endif
        <div class="relative">
            <input
                type="checkbox"
                class="peer sr-only"
                {{ $attributes->merge([
                    'class' => 'peer sr-only',
                    'type' => 'checkbox',
                ]) }}
            />
            <div class="block h-8 rounded-full dark:bg-gray-900 bg-gray-300 w-14 peer-checked:bg-green-600"></div>
            <div
                class="absolute w-6 h-6 transition bg-white rounded-full dot left-1 top-1 peer-checked:translate-x-full">
            </div>
        </div>
    </label>
</div>
