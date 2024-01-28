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
    <div class="flex items-center justify-center h-full mx-2">
        <div class="card w-96 bg-white text-black">
            <div class="card-body items-center text-center flex flex-col gap-4">
                <h2 class="card-title">Sign In</h2>

                {{-- Login Form Fields --}}

                <div class="card-actions justify-end">
                    <button class="btn btn-primary">Login</button>
                </div>
            </div>
        </div>
    </div>
</div>
