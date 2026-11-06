<span class="nav__badge-slot">
    @if ($count > 0)
        <span class="nav__badge">{{ $count > 99 ? '99+' : $count }}<span class="visually-hidden"> unread</span></span>
    @endif
</span>
