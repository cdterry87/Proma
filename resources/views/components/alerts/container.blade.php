<div class="alerts-container flex flex-col gap-2 w-full">
    @if (session()->has('error'))
        <x-alerts.base
            class="alert-error"
            icon="error"
        >
            {{ session('error') }}
        </x-alerts.base>
    @endif

    @if (session()->has('warning'))
        <x-alerts.base
            class="alert-warning"
            icon="warning"
        >
            {{ session('warning') }}
        </x-alerts.base>
    @endif

    @if (session()->has('info'))
        <x-alerts.base
            class="alert-info"
            icon="info"
        >
            {{ session('info') }}
        </x-alerts.base>
    @endif

    @if (session()->has('success'))
        <x-alerts.base
            class="alert-success"
            icon="success"
        >
            {{ session('success') }}
        </x-alerts.base>
    @endif
</div>
