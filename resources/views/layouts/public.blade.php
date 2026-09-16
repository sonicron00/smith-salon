<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ config('app.name', 'Jewel Beauty & Head Spa') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-stone-50 text-stone-900">
    <header class="sticky top-0 z-20 border-b border-rose-100/80 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-1 sm:px-6">
            <a href="{{ route('landing') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/logo2.png') }}" alt="{{ config('app.name') }}" class="h-[140px] w-auto sm:h-[165px]" />
            </a>

            <nav class="flex items-center gap-6 text-base font-semibold tracking-wide text-[#5a3c3a] sm:gap-8">
                <a href="{{ route('landing') }}"
                   class="relative transition duration-200 hover:text-[#c6a268] after:absolute after:-bottom-1 after:left-0 after:h-[2px] after:w-0 after:bg-[#c6a268] after:transition-all after:duration-300 hover:after:w-full">
                    Home
                </a>

                <a href="{{ route('about') }}"
                   class="relative transition duration-200 hover:text-[#c6a268] after:absolute after:-bottom-1 after:left-0 after:h-[2px] after:w-0 after:bg-[#c6a268] after:transition-all after:duration-300 hover:after:w-full">
                    About Us
                </a>

                <a href="{{ route('treatments') }}"
                   class="relative transition duration-200 hover:text-[#c6a268] after:absolute after:-bottom-1 after:left-0 after:h-[2px] after:w-0 after:bg-[#c6a268] after:transition-all after:duration-300 hover:after:w-full">
                    Treatments
                </a>

                <a href="{{ route('booking.start') }}"
                   class="rounded-full border border-[#c6a268] px-4 py-2 text-sm font-semibold text-[#5a3c3a] transition hover:bg-[#c6a268] hover:text-white">
                    Book
                </a>
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-6 sm:px-6 sm:py-10">
        @if (session('status'))
            <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="mt-12 border-t border-rose-100 bg-white">
        <div class="mx-auto flex max-w-6xl flex-col gap-3 px-4 py-6 text-sm text-stone-600 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div class="flex items-center gap-4">
                <span>© {{ date('Y') }} Jewel Beauty &amp; Head Spa Limited</span>
                @php
                    $fbUrl = \App\Models\Setting::get('social.facebook_url');
                    $igUrl = \App\Models\Setting::get('social.instagram_url');
                @endphp
                @if ($fbUrl)
                    <a href="{{ $fbUrl }}" target="_blank" rel="noopener" aria-label="Facebook" class="text-stone-400 transition hover:text-stone-700">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987H7.898V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                        </svg>
                    </a>
                @endif
                @if ($igUrl)
                    <a href="{{ $igUrl }}" target="_blank" rel="noopener" aria-label="Instagram" class="text-stone-400 transition hover:text-stone-700">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"/>
                        </svg>
                    </a>
                @endif
            </div>
            <div>{{ config('salon.address') }}</div>
        </div>
    </footer>
</body>
</html>
