@props(['colspan'])

<tr>
    <td colspan="{{ $colspan }}" class="table__empty">{{ $slot }}</td>
</tr>
