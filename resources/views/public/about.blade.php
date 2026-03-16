@extends('layouts.public')

@section('content')
<div class="grid gap-6 lg:grid-cols-[1.05fr_0.95fr] lg:gap-10">
    <section class="overflow-hidden rounded-[2rem] border border-rose-100 bg-white shadow-sm">
        <img src="{{ asset('images/jewel.png') }}" alt="Salon owners" class="h-full min-h-[300px] w-full object-cover" />
    </section>

    <section class="rounded-[2rem] border border-rose-100 bg-white p-6 shadow-sm sm:p-8">
        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-rose-700">About us</p>
        <h1 class="mt-3 text-3xl font-semibold text-stone-900">A boutique salon experience built around calm, care and beautiful results.</h1>
        <div class="mt-4 space-y-4 text-sm leading-7 text-stone-600 sm:text-base">
            <p>
                Jewel is a Mother-Daughter owned spa, a space designed for total relaxation and elevated self-care.
                We specialise in indulgent head spa rituals and refined brow and lash treatments that leave clients feeling polished, restored and confident.
            </p>
            <p>
                Lauren specialises in brow and lash treatments, including brow waxing and tinting, brow lamination and korean lash lift.
            </p>
            <p>
                Nicola specialises in head spa treatments which comprise a relaxing scalp detox and a deep conditioning treatment.
            </p>
            <p>
                A beauty space specialising in brows, lashes and head spa treatments.
                Bookings are simple, mobile friendly, and easy to manage if plans change.
            </p>
        </div>

        <div class="mt-6 grid gap-6 sm:grid-cols-2">
            <div>
                <h2 class="text-lg font-semibold text-stone-900">Opening hours</h2>
                <div class="mt-3 space-y-2 text-sm text-stone-600">
                    @foreach ($openingHours as $line)
                        <div class="flex items-center justify-between rounded-xl bg-stone-50 px-4 py-3">
                            <span>{{ $line['day'] }}</span>
                            <span class="font-medium text-stone-900">{{ $line['hours'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-stone-900">Find us</h2>
                <p class="mt-3 text-sm leading-7 text-stone-600">{{ $address }}</p>
                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($address) }}" target="_blank" rel="noopener" class="mt-3 inline-flex rounded-full border border-rose-200 px-4 py-2 text-sm font-medium text-rose-800 transition hover:bg-rose-50">Open in Google Maps</a>
            </div>
        </div>
    </section>
</div>
@endsection
