@extends('layouts.public')

@section('content')
<div class="rounded-2xl bg-white p-6 shadow-sm">
    <h1 class="text-xl font-semibold">Choose who you want</h1>
    <p class="mt-1 text-gray-600">{{ $service->name }} ({{ $service->duration_minutes }} mins)</p>

    <div class="mt-4 space-y-3">
        @foreach ($staff as $person)
            <a href="{{ route('booking.slots', ['service' => $service->id, 'staff' => $person->id]) }}"
               class="block rounded-xl border p-4 hover:bg-gray-50">
                <div class="font-medium">{{ $person->name }}</div>
            </a>
        @endforeach
    </div>
</div>
@endsection
