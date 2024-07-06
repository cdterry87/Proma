<footer class="footer footer-center px-4 py-2 bg-base-200 text-base-content">
    <div class="w-full max-w-6xl mx-auto">
        <aside class="flex flex-col gap-1 sm:flex-row sm:items-center">
            <p>&copy; {{ date('Y') }}. All rights reserved.</p>
            <span class="hidden sm:block">|</span>
            <a
                href="{{ route('privacy-policy') }}"
                class="font-bold text-primary"
            >
                Privacy Policy
            </a>
        </aside>
    </div>
</footer>
