<div class="h-screen">
    {{-- Background Video --}}
    <div class="login--video-container video-overlay">
        <video
            autoplay
            muted
            loop
            id="login--background-video"
        >
            <source
                src="{{ asset('videos/coverr-focused-working-space-1555-1080p.mp4') }}"
                type="video/mp4"
            >
            Your browser does not support the video tag.
        </video>
    </div>

    {{-- Login Form --}}
    <div class="flex flex-col gap-4 items-center justify-center h-full mx-2">
        <div class="text-center text-white">
            <h2 class="text-5xl font-bold logo">
                {{ config('app.name') }}
            </h2>
            <p>Simplify your project management.</p>
        </div>

        <div class="card w-full sm:w-96">
            <form wire:submit.prevent="login">
                <div
                    class="card-body items-center text-center flex flex-col gap-4 rounded-3xl"
                    data-theme="light"
                >
                    <h2 class="card-title">Sign In</h2>

                    <x-alerts.container />

                    <div class="flex flex-col gap-2 w-full">
                        <x-inputs.text
                            label="Email"
                            name="email"
                            type="email"
                            placeholder="Email address"
                            wire:model="email"
                            required
                        />
                        <x-inputs.text
                            label="Password"
                            name="password"
                            placeholder="Password"
                            type="password"
                            wire:model="password"
                            required
                        />
                        <x-inputs.checkbox
                            label="Remember Me"
                            name="remember"
                            id="remember"
                            class="checkbox-primary"
                            wire:model="remember"
                        />
                    </div>

                    <div class="card-actions justify-end w-full">
                        <x-inputs.button
                            class="btn-primary w-full"
                            label="Login"
                        />
                    </div>
                </div>
            </form>
        </div>

        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:gap-2">
            <p>Copyright © {{ date('Y') }}. All rights reserved.</p>
            <span class="hidden sm:block">|</span>
            <a
                href="{{ route('privacy-policy') }}"
                class="font-bold text-primary"
            >Privacy Policy</a>
        </div>
    </div>
</div>
