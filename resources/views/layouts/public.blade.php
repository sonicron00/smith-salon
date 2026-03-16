<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ config('app.name', 'Smith Salon Booking') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">
    <header class="bg-white shadow-sm">
        <div class="mx-auto max-w-3xl px-4 py-4 flex items-center justify-between">
            <a href="{{ route('landing') }}" class="flex items-center">
                <img
                        src="{{ asset('images/logo.png') }}"
                        alt="{{ config('app.name') }}"
                        class="h-90 w-auto"
                />
            </a>
            <a href="{{ route('booking.start') }}" class="rounded-lg bg-black text-white px-4 py-2 text-sm">Book</a>
        </div>
    </header>

    <main class="mx-auto max-w-3xl px-4 py-6">
        @if (session('status'))
            <div class="mb-4 rounded-lg bg-green-50 p-3 text-green-800">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 p-3 text-red-800">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="mt-10 border-t bg-white">
        <div class="mx-auto max-w-3xl px-4 py-6 text-sm text-gray-600">
            © {{ date('Y') }} {{ 'Jewel Beauty & Head Spa Limited' }}
        </div>
    </footer>
</body>
</html>
