@extends('layouts.public')

@section('content')
<div class="rounded-2xl bg-white p-6 shadow-sm">
    <h1 class="text-2xl font-semibold">Booked!</h1>
    <p class="mt-2 text-gray-700">
        {{ $appointment->service->name }} with {{ $appointment->staff->name }}<br/>
        <span class="font-medium">{{ $appointment->starts_at->format('D j M Y \a\t H:i') }}</span>
    </p>

    <div class="mt-4 rounded-xl bg-gray-50 p-4">
        <div class="text-sm text-gray-700">Manage your appointment:</div>
        <a class="mt-1 inline-block break-all text-sm font-medium underline" href="{{ $appointment->manageUrl() }}">{{ $appointment->manageUrl() }}</a>
    </div>

    <div class="mt-6">
        <a href="{{ route('landing') }}" class="rounded-xl border px-4 py-2">Back to home</a>
    </div>
</div>
@endsection
