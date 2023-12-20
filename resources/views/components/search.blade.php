@props(['disabled' => false])

<div class="relative">
    <input
        {{ $disabled ? 'disabled' : '' }}
        {!! $attributes->merge([
            'type' => 'search',
            'class' =>
                'w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm pr-10',
        ]) !!}
    >
    <x-icons.search class="h-4 w-4 absolute right-3 top-3 text-gray-300 dark:text-gray-600" />
</div>
