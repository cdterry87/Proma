<x-card>
    <h1 class="guest__title">Forgot password</h1>

    @if ($sent)
        <x-alert variant="success">If that email is registered, a reset link is on its way.</x-alert>
    @else
        <p>Enter your email and we will send you a link to reset your password.</p>

        <form class="form" wire:submit="send">
            <x-input name="email" label="Email" type="email" autocomplete="username" wire:model="email" required autofocus />
            <x-button type="submit" variant="primary" block>Send reset link</x-button>
        </form>
    @endif

    <a href="{{ route('login') }}" wire:navigate>Back to sign in</a>
</x-card>
