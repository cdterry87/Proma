<div class="stack">
    @if ($this->demo)
        <x-alert variant="info" class="demo-notice">
            <h2 class="demo-notice__title">Try the demo</h2>
            <p>Note: the demo data resets daily.</p>
            <x-button block wire:click="loginAsDemo" wire:loading.attr="disabled">Sign in as the demo user</x-button>
        </x-alert>
    @endif

    <x-card>
        <h1 class="guest__title">Sign in</h1>

        <form class="form" wire:submit="login">
            <x-input name="email" label="Email" type="email" autocomplete="username" wire:model="email" required autofocus />
            <x-input name="password" label="Password" type="password" autocomplete="current-password" wire:model="password" required />

            <div class="guest__row">
                <x-checkbox name="remember" label="Remember me" wire:model="remember" />
                <a href="{{ route('password.request') }}" wire:navigate>Forgot password?</a>
            </div>

            <x-button type="submit" variant="primary" block>Sign in</x-button>
        </form>
    </x-card>
</div>
