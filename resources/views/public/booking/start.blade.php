@extends('layouts.public')

@section('content')
<div class="rounded-2xl bg-white p-6 shadow-sm">
    <h1 class="text-xl font-semibold">Choose a service</h1>

    <div class="mt-4 space-y-3">
        @foreach ($services as $service)
            <a href="{{ route('booking.staff', ['service' => $service->id]) }}"
               class="block rounded-xl border p-4 hover:bg-gray-50">
                <div class="font-medium">{{ $service->name }}</div>
                <div class="text-sm text-gray-600">{{ $service->duration_minutes }} mins</div>
            </a>
        @endforeach
    </div>
</div>
@endsection
