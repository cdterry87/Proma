@props(['disabled' => false, 'options' => [], 'withoutDefault' => false])

<select
    {{ $disabled ? 'disabled' : '' }}
    {!! $attributes->merge([
        'class' =>
            'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm pr-8',
    ]) !!}
>
    @if (!$withoutDefault)
        <option value="">Select an option</option>
    @endif
    @if ($options)
        @foreach ($options as $key => $value)
            <option value="{{ $key }}">{{ $value }}</option>
        @endforeach
    @else
        {{ $slot }}
    @endif
</select>
