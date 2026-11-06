<div class="sidebar__user">
    <p class="sidebar__user-name">{{ $user->name }}</p>
    @if ($user->title)
        <p class="sidebar__user-title">{{ $user->title }}</p>
    @endif
</div>
