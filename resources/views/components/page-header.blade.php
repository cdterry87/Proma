@props(['title', 'actions' => null])

<header {{ $attributes->class('page-header') }}>
    <h1>{{ $title }}</h1>
    @if ($actions)
        <div class="page-header__actions">{{ $actions }}</div>
    @endif
</header>
