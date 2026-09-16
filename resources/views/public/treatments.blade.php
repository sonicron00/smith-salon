@extends('layouts.public')

@section('content')
    <div class="mx-auto max-w-4xl space-y-8">
        <div class="rounded-[2rem] border border-rose-100 bg-white p-6 shadow-sm sm:p-8">
            <h1 class="text-3xl font-semibold text-stone-900">Treatments & Prices</h1>
            <p class="mt-2 text-sm leading-7 text-stone-600">
                Our full range of head spa, lash and brow treatments. Book online in just a few taps.
            </p>

            <div class="mt-6 divide-y divide-rose-100">
                @forelse ($services as $service)
                    <div class="flex items-start justify-between gap-4 py-4">
                        <div>
                            <div class="font-medium text-stone-900">{{ $service->name }}</div>
                            <div class="mt-1 text-sm text-stone-600">{{ $service->duration_minutes }} mins</div>
                        </div>
                        <div class="shrink-0 text-right">
                            <div class="text-sm font-semibold text-stone-900">
                                £{{ number_format(($service->price_pence ?? 0) / 100, 2) }}
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="py-4 text-sm text-stone-600">Treatments will be listed here soon.</p>
                @endforelse
            </div>

            <div class="mt-6">
                <a href="{{ route('booking.start') }}" class="inline-flex items-center rounded-full bg-stone-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-black">
                    Book a treatment
                </a>
            </div>
        </div>

        @php
            $treatmentImageOne = $imageOne ?: asset('images/bed.jpg');
            $treatmentImageTwo = $imageTwo ?: asset('images/tap.jpg');
        @endphp

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="overflow-hidden rounded-[1.75rem]">
                <img
                    src="{{ $treatmentImageOne }}"
                    alt="Our salon"
                    class="h-72 w-full rounded-[1.75rem] object-cover sm:h-80"
                />
            </div>
            <div class="overflow-hidden rounded-[1.75rem]">
                <img
                    src="{{ $treatmentImageTwo }}"
                    alt="Our salon"
                    class="h-72 w-full rounded-[1.75rem] object-cover sm:h-80"
                />
            </div>
        </div>
    </div>
@endsection
