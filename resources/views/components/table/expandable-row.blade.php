@props(['colspan', 'detail', 'label' => 'Show details'])

<tbody x-data="disclosure" {{ $attributes }}>
    <tr class="table__row">
        <td class="table__cell table__cell--toggle">
            <button type="button" class="table__toggle" aria-label="{{ $label }}" x-bind:aria-expanded="open" x-on:click="toggle">
                <x-icon name="chevron-right" size="sm" />
            </button>
        </td>
        {{ $slot }}
    </tr>
    <tr class="table__detail" x-show="open" x-cloak>
        <td colspan="{{ $colspan }}" class="table__cell text-pre">{{ $detail }}</td>
    </tr>
</tbody>
