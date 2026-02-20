@extends('layouts.public')

@section('content')
<div class="space-y-6">
    <section class="rounded-2xl bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-semibold">Book your appointment</h1>
        <p class="mt-2 text-gray-600">Choose a service, pick your stylist, and find a time that works for you.</p>
        <div class="mt-4">
            <a href="{{ route('booking.start') }}" class="inline-flex items-center rounded-xl bg-black px-5 py-3 text-white">Start booking</a>
        </div>
    </section>

    <section class="rounded-2xl bg-white p-6 shadow-sm">
        <h2 class="text-xl font-semibold">Instagram</h2>
        @if (!empty($instagramEmbedHtml))
            <div class="mt-3">
                {!! $instagramEmbedHtml !!}
            </div>
        @elseif (!empty($instagramProfileUrl))
            <p class="mt-2 text-gray-600">Follow us on Instagram for updates and latest work.</p>
            <a class="mt-3 inline-block rounded-xl border px-4 py-2" href="{{ $instagramProfileUrl }}" target="_blank" rel="noopener">Open Instagram</a>
        @else
            <p class="mt-2 text-gray-600">Instagram link not set yet.</p>
        @endif
    </section>

    <section class="rounded-2xl bg-white p-6 shadow-sm">
        <h2 class="text-xl font-semibold">Cancellation policy</h2>
        <div class="prose mt-3 max-w-none">
            {!! $cancellationPolicyHtml ?: '<p>Please contact us if you need to cancel or reschedule.</p>' !!}
        </div>
    </section>
</div>
@endsection
