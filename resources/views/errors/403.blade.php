<x-layouts.error-page-template
    icon="icons.error-403"
    message="Sorry, you are not authorized to access this page."
>
    <a
        href="{{ route('home') }}"
        class="btn btn-primary mt-6"
    >
        <x-icons.arrow-left class="w-6 h-6" />
        Return Home
    </a>
</x-layouts.error-page-template>
