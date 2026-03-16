<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ config('app.name', 'Jewel Beauty & Head Spa') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-50 text-stone-900">
    <header class="sticky top-0 z-20 border-b border-rose-100/80 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-1 sm:px-6">
            <a href="{{ route('landing') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/logo2.png') }}" alt="{{ config('app.name') }}" class="h-28 w-auto sm:h-32" />
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
        <div class="mx-auto flex max-w-6xl flex-col gap-2 px-4 py-6 text-sm text-stone-600 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>© {{ date('Y') }} Jewel Beauty & Head Spa Limited</div>
            <div>{{ config('salon.address') }}</div>
        </div>
    </footer>
</body>
</html>
