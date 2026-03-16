@extends('layouts.public')

@section('content')
<div class="mx-auto max-w-3xl rounded-[2rem] border border-rose-100 bg-white p-6 shadow-sm sm:p-8">
    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-rose-700">Step 2</p>
    <h1 class="mt-3 text-3xl font-semibold text-stone-900">Choose your specialist</h1>
    <p class="mt-2 text-sm leading-7 text-stone-600">{{ $service->name }} · {{ $service->duration_minutes }} mins</p>

    <div class="mt-6 space-y-3">
        @foreach ($staff as $person)
            <a href="{{ route('booking.slots', ['service' => $service->id, 'staff' => $person->id]) }}"
               class="block rounded-[1.25rem] border border-rose-100 p-4 transition hover:-translate-y-0.5 hover:border-rose-200 hover:bg-rose-50/50">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <div class="font-semibold text-stone-900">{{ $person->name }}</div>
                        <div class="mt-1 text-sm text-stone-600">Choose this team member</div>
                    </div>
                    <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-700">Select</span>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection
