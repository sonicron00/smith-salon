@extends('layouts.public')

@section('content')
<div class="space-y-6">
    <section class="rounded-2xl bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-semibold">Your appointment</h1>

        <div class="mt-3 text-gray-700">
            <div><span class="font-medium">Service:</span> {{ $appointment->service->name }}</div>
            <div><span class="font-medium">Staff:</span> {{ $appointment->staff->name }}</div>
            <div><span class="font-medium">When:</span> {{ $appointment->starts_at->format('D j M Y \a\t H:i') }}</div>
            <div><span class="font-medium">Status:</span> {{ ucfirst($appointment->status) }}</div>
        </div>

        @if (!$canModify)
            <div class="mt-4 rounded-xl bg-amber-50 p-3 text-amber-800">
                Changes are not allowed within {{ $cutoffHours }} hours of the appointment.
            </div>
        @endif
    </section>

    <section class="rounded-2xl bg-white p-6 shadow-sm">
        <h2 class="text-xl font-semibold">Reschedule</h2>

        <form class="mt-3" method="get" action="{{ route('appointment.manage', ['token' => $appointment->manage_token]) }}">
            <label class="text-sm text-gray-700">Pick a date</label>
            <input type="date" name="date" value="{{ $date }}" class="mt-1 w-full rounded-xl border px-3 py-2" />
            <button class="mt-3 rounded-xl border px-4 py-2">Show times</button>
        </form>

        @if ($canModify)
            <form class="mt-4 space-y-3" method="post" action="{{ route('appointment.reschedule', ['token' => $appointment->manage_token]) }}">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}" />
                <label class="text-sm text-gray-700">Available times</label>
                <select name="time" class="w-full rounded-xl border px-3 py-2">
                    @foreach ($slots as $t)
                        <option value="{{ $t }}">{{ $t }}</option>
                    @endforeach
                </select>
                <button class="w-full rounded-xl bg-black px-4 py-3 text-white">Reschedule</button>
            </form>
        @else
            <p class="mt-3 text-sm text-gray-600">Rescheduling is disabled due to the cutoff window.</p>
        @endif
    </section>

    <section class="rounded-2xl bg-white p-6 shadow-sm">
        <h2 class="text-xl font-semibold">Cancel</h2>

        @if ($canModify)
            <form class="mt-3 space-y-3" method="post" action="{{ route('appointment.cancel', ['token' => $appointment->manage_token]) }}">
                @csrf
                <label class="text-sm text-gray-700">Reason (optional)</label>
                <input name="reason" class="w-full rounded-xl border px-3 py-2" />
                <button class="w-full rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">Cancel appointment</button>
            </form>
        @else
            <p class="mt-3 text-sm text-gray-600">Cancellation is disabled due to the cutoff window.</p>
        @endif
    </section>

    <section class="rounded-2xl bg-white p-6 shadow-sm">
        <h2 class="text-xl font-semibold">Cancellation policy</h2>
        <div class="prose mt-3 max-w-none">
            {!! $cancellationPolicyHtml ?: '<p>Please contact us if you need to cancel or reschedule.</p>' !!}
        </div>
    </section>
</div>
@endsection
