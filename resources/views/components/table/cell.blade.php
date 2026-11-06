@props(['label' => null])

<td @if ($label) data-label="{{ $label }}" @endif {{ $attributes->class('table__cell') }}>{{ $slot }}</td>
