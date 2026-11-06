@php
    $links = [
        ['label' => 'Home', 'href' => '/', 'icon' => 'home', 'active' => '/'],
        ['label' => 'Projects', 'href' => '/projects', 'icon' => 'projects', 'active' => 'projects*'],
        ['label' => 'Issues', 'href' => '/issues', 'icon' => 'issues', 'active' => 'issues*'],
        ['label' => 'Clients', 'href' => '/clients', 'icon' => 'clients', 'active' => 'clients*'],
        ['label' => 'Notifications', 'href' => '/notifications', 'icon' => 'notifications', 'active' => 'notifications*'],
        ['label' => 'Settings', 'href' => '/settings', 'icon' => 'settings', 'active' => 'settings*'],
    ];
@endphp

<aside id="sidebar" class="sidebar" x-bind:data-open="open">
    <a href="{{ url('/') }}" class="logo sidebar__logo" wire:navigate>{{ config('app.name') }}</a>

    <nav class="nav" aria-label="Main">
        @foreach ($links as $link)
            <a
                href="{{ url($link['href']) }}"
                class="nav__link"
                wire:navigate
                @if (request()->is($link['active'])) aria-current="page" @endif
            >
                <x-icon :name="$link['icon']" />
                {{ $link['label'] }}
            </a>
        @endforeach
    </nav>
</aside>

<div class="sidebar__overlay" x-show="open" x-cloak x-on:click="close"></div>
