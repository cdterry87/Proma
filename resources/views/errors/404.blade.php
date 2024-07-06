<x-layouts.error-page-template
    icon="icons.error-404"
    message="Sorry, we could not find the page you were looking for. Please return to the home page and try again."
>
    <a
        href="{{ route('home') }}"
        class="btn btn-primary mt-6"
    >
        <x-icons.arrow-left class="w-6 h-6" />
        Return Home
    </a>
</x-layouts.error-page-template>
