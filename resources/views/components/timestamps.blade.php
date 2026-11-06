@props(['created', 'updated' => null, 'format' => 'm/d/Y H:i'])

<div {{ $attributes->class('timestamps') }}>
    <span>Created: {{ $created->format($format) }}</span>
    @if ($updated && ! $updated->equalTo($created))
        <span>Updated: {{ $updated->format($format) }}</span>
    @endif
</div>
