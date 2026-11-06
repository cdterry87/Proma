@props(['rows', 'perPageOptions', 'idPrefix' => 'table'])

<div {{ $attributes->class('table-footer') }}>
    <x-select name="perPage" :id="$idPrefix.'-per-page'" label="Rows per page" hide-label wire:model.live="perPage">
        @foreach ($perPageOptions as $option)
            <option value="{{ $option }}">{{ $option }} per page</option>
        @endforeach
    </x-select>

    <x-pagination :paginator="$rows" />
</div>
