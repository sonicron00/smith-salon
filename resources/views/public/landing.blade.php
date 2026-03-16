@extends('layouts.public')

@section('content')
<div class="space-y-6 sm:space-y-10">
    <section class="overflow-hidden rounded-[2rem] bg-gradient-to-b from-[#f4e8e8] via-[#e7c3c3] to-[#d8abab] text-[#4d302f] shadow-xl">
        <div class="grid gap-6 px-6 py-8 sm:px-10 sm:py-12 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#9a6f6f]">Luxury beauty space & head spa</p>
                <h1 class="mt-4 text-4xl font-semibold leading-tight sm:text-5xl">Head spa, brows and lashes in a calm, luxurious space.</h1>
                <p class="mt-4 max-w-2xl text-sm leading-7 text-[#6f4d4d] sm:text-base">
                    Treat yourself to elevated head spa treatments and beautifully tailored brow and lash appointments.
                    Mobile-friendly booking, easy rescheduling, and a warm boutique feel from the very first click.
                </p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('booking.start') }}" class="inline-flex items-center rounded-full bg-white px-5 py-3 text-sm font-semibold text-stone-900 transition hover:bg-rose-50">Start booking</a>
                    <a href="{{ route('about') }}" class="inline-flex items-center rounded-full bg-white px-5 py-3 text-sm font-semibold text-stone-900 transition hover:bg-rose-50">About us</a>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
                <div class="rounded-[1.75rem] bg-white/45 p-5 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.25em] text-[#9a6f6f]">Signature treatments</p>
                    <ul class="mt-4 space-y-3 text-sm text-[#5f4141]">
                        <li>• Luxury head spa rituals</li>
                        <li>• Brow shaping and tinting</li>
                        <li>• Lash lift and lash enhancement</li>
                    </ul>
                </div>
                <div class="rounded-[1.75rem] border border-white/40 bg-white/30 p-6 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.25em] text-[#9a6f6f]">A boutique experience</p>
                    <p class="mt-3 text-sm leading-7 text-[#5f4141]">
                        Calm, beautifully presented treatments with a luxury feel from booking to aftercare.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-[1.5rem] border border-rose-100 bg-white p-5 shadow-sm">
            <div class="text-sm font-semibold text-stone-900">Choose a treatment</div>
            <p class="mt-2 text-sm leading-6 text-stone-600">Pick from head spa, lash and brow services with clear durations.</p>
        </div>
        <div class="rounded-[1.5rem] border border-rose-100 bg-white p-5 shadow-sm">
            <div class="text-sm font-semibold text-stone-900">Pick your specialist</div>
            <p class="mt-2 text-sm leading-6 text-stone-600">Book directly with the owner or team member you want.</p>
        </div>
        <div class="rounded-[1.5rem] border border-rose-100 bg-white p-5 shadow-sm">
            <div class="text-sm font-semibold text-stone-900">Manage by text link</div>
            <p class="mt-2 text-sm leading-6 text-stone-600">Clients can reschedule or cancel from their booking link without calling.</p>
        </div>
    </section>

    <section class="grid gap-6 lg:grid-cols-[1fr_0.9fr]">
        <div class="rounded-[1.75rem] border border-rose-100 bg-white p-6 shadow-sm">
            <h2 class="text-2xl font-semibold text-stone-900">Visit us</h2>
            <p class="mt-3 text-sm leading-7 text-stone-600">{{ config('salon.address') }}</p>
            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode(config('salon.address')) }}" target="_blank" rel="noopener" class="mt-4 inline-flex rounded-full border border-rose-200 px-4 py-2 text-sm font-medium text-rose-800 transition hover:bg-rose-50">Open in Google Maps</a>

            <div class="mt-6 grid gap-2 text-sm text-stone-600">
                @foreach (config('salon.opening_hours') as $line)
                    <div class="flex items-center justify-between rounded-xl bg-stone-50 px-4 py-3">
                        <span>{{ $line['day'] }}</span>
                        <span class="font-medium text-stone-900">{{ $line['hours'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-[1.75rem] border border-rose-100 bg-white p-3 shadow-sm">
            <iframe
                title="Salon map"
                class="h-[320px] w-full rounded-[1.25rem] border-0"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                src="https://www.google.com/maps?q={{ urlencode(config('salon.address')) }}&z=15&output=embed">
            </iframe>
        </div>
    </section>
</div>
@endsection
