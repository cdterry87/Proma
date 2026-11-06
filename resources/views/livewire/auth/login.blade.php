<div class="stack">
    @if (config('proma.demo.enabled') && filled(config('proma.demo.email')) && filled(config('proma.demo.password')))
        <x-alert variant="info">
            <p>Try the demo with these credentials:</p>
            <p>Email: <strong>{{ config('proma.demo.email') }}</strong></p>
            <p>Password: <strong>{{ config('proma.demo.password') }}</strong></p>
            <p><small>All demo settings and data reset daily.</small></p>
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
