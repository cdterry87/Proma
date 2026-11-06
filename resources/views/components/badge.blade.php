@props(['variant' => null])

<span {{ $attributes->class(['badge', 'badge--'.$variant => $variant]) }}>{{ $slot }}</span>
