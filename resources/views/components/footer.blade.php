<div
    class="flex flex-col items-center gap-2 bg-white text-gray-400 dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 py-8">
    <div>
        &copy; {{ date('Y') }} {{ config('app.name') }}. Developed by Chase Terry.
    </div>
    <div>
        <a
            href="https://chaseterry.com"
            target="_blank"
            rel="noreferer"
            class="font-bold"
        >
            https://chaseterry.com
        </a>
    </div>
    <x-alert />
</div>
