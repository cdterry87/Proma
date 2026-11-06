<div
    class="toasts"
    aria-live="polite"
    x-data="toasts"
    data-flash="{{ session()->has('toast') ? json_encode(session('toast')) : '' }}"
>
    <template x-for="toast in items" x-bind:key="toast.id">
        <div class="toast" x-bind:class="classFor(toast)">
            <span x-text="toast.message"></span>
            <button type="button" class="toast__close" aria-label="Dismiss" x-on:click="dismiss(toast.id)">
                <x-icon name="x" size="sm" />
            </button>
        </div>
    </template>
</div>
