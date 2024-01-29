<footer class="footer footer-center p-4 bg-base-300 text-base-content">
    <aside class="flex flex-col gap-1 sm:flex-row sm:items-center sm:gap-2">
        <p>Copyright © {{ date('Y') }}. All rights reserved.</p>
        <span class="hidden sm:block">|</span>
        <a
            href="{{ route('privacy-policy') }}"
            class="font-bold text-primary"
        >Privacy Policy</a>
    </aside>
</footer>
