<div class="sidebar__user">
    <span class="sidebar__avatar" aria-hidden="true">{{ $user->initials() }}</span>
    <div class="sidebar__user-text">
        <p class="sidebar__user-name" title="{{ $user->name }}">{{ $user->name }}</p>
        @if ($user->title)
            <p class="sidebar__user-title" title="{{ $user->title }}">{{ $user->title }}</p>
        @endif
    </div>
</div>
