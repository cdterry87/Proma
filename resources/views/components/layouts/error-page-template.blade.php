@props(['icon', 'message'])

<x-layouts.guest>
    <div
        x-data="{
            light: false,
            setTheme(theme) {
                document.documentElement.setAttribute('data-theme', theme);
                document.documentElement.classList[theme === 'dark' ? 'add' : 'remove']('dark');
            },
            init() {
                const theme = localStorage.getItem('theme') || 'dark';
                this.light = theme === 'light';
                this.setTheme(theme);
            }
        }"
        class="w-full h-screen flex flex-col items-center justify-between bg-base-200"
    >
        <div class="xl:w-1/2 flex-1 flex flex-col gap-6 items-center justify-center text-center px-4 lg:px-0">
            @if ($icon)
                <x-dynamic-component
                    :component="$icon"
                    class="w-56 h-auto"
                />
            @endif

            <div>
                <h1 class="logo text-4xl font-bold select-none">
                    {{ config('app.name') }}
                </h1>
                @if ($message)
                    <p class="text-lg mt-4 w-full sm:w-96">
                        {{ $message }}
                    </p>
                @endif
            </div>

            {{ $slot }}
        </div>
        <div class="w-full bg-base-300 p-4">
            <div
                class="container mx-auto flex flex-col md:flex-row items-center justify-center text-center text-sm md:space-x-8 space-y-1 md:space-y-0 gap-4">
                <div>
                    Please contact us if you continue to experience issues:
                </div>
                <a
                    href="#"
                    class="flex items-center space-x-1 gap-1 font-bold"
                    target="_blank"
                    title="Email"
                >
                    <x-icons.mail class="h-6 w-6" />
                    <div>support@proma.com</div>
                </a>
            </div>
        </div>
    </div>
</x-layouts.guest>
