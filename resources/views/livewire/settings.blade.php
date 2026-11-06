<div class="page">
    <x-page-header title="Settings" />

    @if ($user->is_demo)
        <x-alert variant="info">Demo accounts cannot change their email, password, or notification settings.</x-alert>
    @endif

    <x-card title="Name">
        @if ($editing === 'name')
            <form class="form" wire:submit="saveName">
                <x-input name="name" label="Name" autocomplete="name" wire:model="name" required />
                <div class="form__actions">
                    <x-button wire:click="cancel">Cancel</x-button>
                    <x-button type="submit" variant="primary">Save</x-button>
                </div>
            </form>
        @else
            <div class="setting">
                <p class="setting__value">{{ $user->name }}</p>
                <x-button icon="edit" wire:click="edit('name')">Edit</x-button>
            </div>
        @endif
    </x-card>

    <x-card title="Title">
        @if ($editing === 'title')
            <form class="form" wire:submit="saveTitle">
                <x-input name="title" label="Title" autocomplete="organization-title" wire:model="title" />
                <div class="form__actions">
                    <x-button wire:click="cancel">Cancel</x-button>
                    <x-button type="submit" variant="primary">Save</x-button>
                </div>
            </form>
        @else
            <div class="setting">
                <p class="setting__value">{{ $user->title ?: 'Not set' }}</p>
                <x-button icon="edit" wire:click="edit('title')">Edit</x-button>
            </div>
        @endif
    </x-card>

    <x-card title="Email">
        @if ($editing === 'email')
            <form class="form" wire:submit="saveEmail">
                <x-input name="email" label="Email" type="email" autocomplete="email" wire:model="email" required />
                <x-input name="current_password" label="Current password" type="password" autocomplete="current-password" wire:model="current_password" required />
                <div class="form__actions">
                    <x-button wire:click="cancel">Cancel</x-button>
                    <x-button type="submit" variant="primary">Save</x-button>
                </div>
            </form>
        @else
            <div class="setting">
                <p class="setting__value">{{ $user->email }}</p>
                @can('changeCredentials', $user)
                    <x-button icon="edit" wire:click="edit('email')">Edit</x-button>
                @endcan
            </div>
        @endif
    </x-card>

    <x-card title="Password">
        @if ($editing === 'password')
            <form class="form" wire:submit="savePassword">
                <x-input name="current_password" label="Current password" type="password" autocomplete="current-password" wire:model="current_password" required />
                <x-input name="password" label="New password" type="password" autocomplete="new-password" wire:model="password" required />
                <x-input name="password_confirmation" label="Confirm new password" type="password" autocomplete="new-password" wire:model="password_confirmation" required />
                <div class="form__actions">
                    <x-button wire:click="cancel">Cancel</x-button>
                    <x-button type="submit" variant="primary">Save</x-button>
                </div>
            </form>
        @else
            <div class="setting">
                <p class="setting__value">Changing your password signs out your other devices.</p>
                @can('changeCredentials', $user)
                    <x-button icon="edit" wire:click="edit('password')">Edit</x-button>
                @endcan
            </div>
        @endif
    </x-card>

    @can('changeEmailNotifications', $user)
        <x-card title="Email notifications">
            <x-toggle name="email_notifications" label="Email me about due dates and overdue work" wire:model.live="emailNotifications" />
        </x-card>
    @endcan
</div>
