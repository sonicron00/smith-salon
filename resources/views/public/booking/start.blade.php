@extends('layouts.public')

@section('content')
<div class="mx-auto max-w-3xl rounded-[2rem] border border-rose-100 bg-white p-6 shadow-sm sm:p-8">
    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-rose-700">Step 1</p>
    <h1 class="mt-3 text-3xl font-semibold text-stone-900">Choose a treatment</h1>
    <p class="mt-2 text-sm leading-7 text-stone-600">Pick the service you would like to book. Everything below is designed to feel simple and easy on mobile.</p>

    <div class="mt-6 space-y-3">
        @foreach ($services as $service)
            <a href="{{ route('booking.staff', ['service' => $service->id]) }}"
               class="block rounded-[1.25rem] border border-rose-100 p-4 transition hover:-translate-y-0.5 hover:border-rose-200 hover:bg-rose-50/50">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="font-semibold text-stone-900">{{ $service->name }}</div>
                        <div class="mt-1 text-sm text-stone-600">{{ $service->duration_minutes }} mins</div>
                    </div>
                    <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-700">Select</span>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection
