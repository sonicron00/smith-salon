@extends('layouts.public')

@section('content')
    <style>
        .treatment-description p { margin-bottom: 0.5rem; }
        .treatment-description p:last-child { margin-bottom: 0; }
        .treatment-description strong { font-weight: 600; color: #1c1917; }
        .treatment-description em { font-style: italic; }
        .treatment-description h3 { font-size: 1rem; font-weight: 600; color: #1c1917; margin-top: 0.75rem; margin-bottom: 0.25rem; }
        .treatment-description ul { list-style: disc; padding-left: 1.25rem; margin-bottom: 0.5rem; }
        .treatment-description ol { list-style: decimal; padding-left: 1.25rem; margin-bottom: 0.5rem; }
        .treatment-description li { margin-bottom: 0.25rem; }
        .treatment-description a { color: #9f1239; text-decoration: underline; }
    </style>

    <div class="mx-auto max-w-4xl space-y-8">
        <div class="rounded-[2rem] border border-rose-100 bg-white p-6 shadow-sm sm:p-8">
            <h1 class="text-3xl font-semibold text-stone-900">Treatments & Prices</h1>
            <p class="mt-2 text-sm leading-7 text-stone-600">
                Our full range of head spa, lash and brow treatments. Book online in just a few taps.
            </p>

            <div class="mt-6 divide-y divide-rose-100">
                @forelse ($services as $service)
                    <div class="py-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-stone-900">{{ $service->name }}</span>
                                    @if ($service->is_addon)
                                        <span class="rounded-full bg-rose-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-rose-700">Add-on</span>
                                    @endif
                                </div>
                                <div class="mt-1 text-sm text-stone-600">{{ $service->duration_minutes }} mins</div>
                            </div>
                            <div class="shrink-0 text-right">
                                <div class="text-sm font-semibold text-stone-900">
                                    £{{ number_format(($service->price_pence ?? 0) / 100, 2) }}
                                </div>
                            </div>
                        </div>

                        @if (!empty($service->description))
                            <div class="treatment-description mt-3 text-sm leading-7 text-stone-600">
                                {!! $service->description !!}
                            </div>
                        @endif
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
