<div
    class="toasts"
    aria-live="polite"
    x-data="toasts"
    data-flash="{{ session()->has('toast') ? json_encode(session('toast')) : '' }}"
>
    <template x-for="toast in items" x-bind:key="toast.id">
        <div class="toast" x-bind:class="classFor(toast)">
            <x-icon name="info" class="toast__icon toast__icon--info" />
            <x-icon name="success" class="toast__icon toast__icon--success" />
            <x-icon name="warning" class="toast__icon toast__icon--warning" />
            <x-icon name="error" class="toast__icon toast__icon--error" />
            <span x-text="toast.message"></span>
            <button type="button" class="toast__close" aria-label="Dismiss" x-on:click="dismiss(toast.id)">
                <x-icon name="x" size="sm" />
            </button>
        </div>
    </template>
</div>
