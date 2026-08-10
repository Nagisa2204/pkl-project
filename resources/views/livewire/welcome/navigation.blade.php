<nav class="-mx-3 flex flex-1 justify-end">
    @auth
        <a
            href="{{ url('/dashboard') }}"
            class="rounded-md px-3 py-2 text-content ring-1 ring-transparent transition hover:text-content/80 focus:outline-none focus-visible:ring-primary dark:text-primary-foreground dark:hover:text-primary-foreground/80 dark:focus-visible:ring-light"
        >
            Dashboard
        </a>
    @else
        <a
            href="{{ route('login') }}"
            class="rounded-md px-3 py-2 text-content ring-1 ring-transparent transition hover:text-content/80 focus:outline-none focus-visible:ring-primary dark:text-primary-foreground dark:hover:text-primary-foreground/80 dark:focus-visible:ring-light"
        >
            Log in
        </a>

        @if (Route::has('register'))
            <a
                href="{{ route('register') }}"
                class="rounded-md px-3 py-2 text-content ring-1 ring-transparent transition hover:text-content/80 focus:outline-none focus-visible:ring-primary dark:text-primary-foreground dark:hover:text-primary-foreground/80 dark:focus-visible:ring-light"
            >
                Register
            </a>
        @endif
    @endauth
</nav>
