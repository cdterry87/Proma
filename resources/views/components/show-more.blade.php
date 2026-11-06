@props(['text', 'limit' => 150])

@php
    $isLong = mb_strlen($text) > $limit;
@endphp

@if ($isLong)
    <div x-data="disclosure" {{ $attributes->class('show-more') }}>
        <p class="text-pre" x-show="!open">{{ Str::limit($text, $limit) }}</p>
        <p class="text-pre" x-show="open" x-cloak>{{ $text }}</p>
        <button type="button" class="show-more__toggle" x-bind:aria-expanded="open" x-on:click="toggle">
            <span x-show="!open">Show more</span>
            <span x-show="open" x-cloak>Show less</span>
        </button>
    </div>
@else
    <p {{ $attributes->class('text-pre') }}>{{ $text }}</p>
@endif
