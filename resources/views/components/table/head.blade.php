@props(['sortable' => null, 'sort' => null, 'direction' => 'asc'])

@php
    $active = $sortable && $sort === $sortable;
    $ariaSort = $active ? ($direction === 'asc' ? 'ascending' : 'descending') : null;
@endphp

<th scope="col" @if ($ariaSort) aria-sort="{{ $ariaSort }}" @endif {{ $attributes->class('table__heading') }}>
    @if ($sortable)
        <button type="button" class="table__sort" wire:click="sortBy('{{ $sortable }}')">
            {{ $slot }}
            @if ($active)
                <x-icon :name="$direction === 'asc' ? 'chevron-up' : 'chevron-down'" size="sm" />
            @endif
        </button>
    @else
        {{ $slot }}
    @endif
</th>
