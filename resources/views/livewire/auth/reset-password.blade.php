<x-card>
    <h1 class="guest__title">Reset password</h1>

    <form class="form" wire:submit="resetPassword">
        <x-input name="email" label="Email" type="email" autocomplete="username" wire:model="email" required />
        <x-input name="password" label="New password" type="password" autocomplete="new-password" wire:model="password" required autofocus />
        <x-input name="password_confirmation" label="Confirm new password" type="password" autocomplete="new-password" wire:model="password_confirmation" required />
        <x-button type="submit" variant="primary" block>Reset password</x-button>
    </form>

    <a href="{{ route('login') }}" wire:navigate>Back to sign in</a>
</x-card>
