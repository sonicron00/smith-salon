@extends('layouts.public')

@section('content')
<div class="mx-auto max-w-2xl rounded-[2rem] border border-rose-100 bg-white p-6 text-center shadow-sm sm:p-8">
    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-rose-100 text-2xl">✨</div>
    <h1 class="mt-4 text-3xl font-semibold text-stone-900">Booked!</h1>
    <p class="mt-3 text-sm leading-7 text-stone-600 sm:text-base">
        {{ $appointment->service->name }} with {{ $appointment->staff->name }}<br/>
        <span class="font-semibold text-stone-900">{{ $appointment->starts_at->format('D j M Y \a\t H:i') }}</span>
    </p>

    <div class="mt-6 flex justify-center gap-3">
        <a href="{{ route('landing') }}" class="rounded-full border border-rose-200 px-4 py-2 text-sm font-medium text-rose-800">Back to home</a>
        <a href="{{ route('booking.start') }}" class="rounded-full bg-stone-900 px-4 py-2 text-sm font-medium text-white">Book another</a>
    </div>
</div>
@endsection
