@extends('layouts.public')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <section class="rounded-[2rem] border border-rose-100 bg-white p-6 shadow-sm sm:p-8">
        <h1 class="text-3xl font-semibold text-stone-900">Manage your appointment</h1>

        <div class="mt-4 grid gap-3 text-sm text-stone-700 sm:grid-cols-2">
            <div class="rounded-[1rem] bg-stone-50 px-4 py-3"><span class="font-semibold">Service:</span> {{ $appointment->service->name }}</div>
            <div class="rounded-[1rem] bg-stone-50 px-4 py-3"><span class="font-semibold">Staff:</span> {{ $appointment->staff->name }}</div>
            <div class="rounded-[1rem] bg-stone-50 px-4 py-3 sm:col-span-2"><span class="font-semibold">When:</span> {{ $appointment->starts_at->format('D j M Y \a\t H:i') }}</div>
            <div class="rounded-[1rem] bg-stone-50 px-4 py-3"><span class="font-semibold">Status:</span> {{ ucfirst($appointment->status) }}</div>
        </div>

        @if (!$canModify)
            <div class="mt-4 rounded-[1rem] border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800">
                Changes are not allowed within {{ $cutoffHours }} hours of the appointment.
            </div>
        @endif
    </section>

    <section class="rounded-[2rem] border border-rose-100 bg-white p-6 shadow-sm sm:p-8">
        <h2 class="text-2xl font-semibold text-stone-900">Reschedule</h2>

        <form class="mt-4" method="get" action="{{ route('appointment.manage', ['token' => $appointment->manage_token]) }}">
            <label class="text-sm font-medium text-stone-700">Pick a date</label>
            <input type="date" name="date" value="{{ $date }}" class="mt-2 w-full rounded-xl border border-rose-100 px-3 py-3" />
            <button class="mt-3 rounded-full border border-rose-200 px-4 py-2 text-sm font-medium text-rose-800">Show times</button>
        </form>

        @if ($canModify)
            <form class="mt-5 space-y-3" method="post" action="{{ route('appointment.reschedule', ['token' => $appointment->manage_token]) }}">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}" />
                <label class="text-sm font-medium text-stone-700">Available times</label>
                <select name="time" class="w-full rounded-xl border border-rose-100 px-3 py-3">
                    @foreach ($slots as $t)
                        <option value="{{ $t }}">{{ $t }}</option>
                    @endforeach
                </select>
                <button class="w-full rounded-full bg-stone-900 px-4 py-3 text-sm font-semibold text-white">Reschedule</button>
            </form>
        @else
            <p class="mt-3 text-sm text-stone-600">Rescheduling is disabled due to the cutoff window.</p>
        @endif
    </section>

    <section class="rounded-[2rem] border border-rose-100 bg-white p-6 shadow-sm sm:p-8">
        <h2 class="text-2xl font-semibold text-stone-900">Cancel</h2>

        @if ($canModify)
            <form class="mt-4 space-y-3" method="post" action="{{ route('appointment.cancel', ['token' => $appointment->manage_token]) }}">
                @csrf
                <label class="text-sm font-medium text-stone-700">Reason (optional)</label>
                <input name="reason" class="w-full rounded-xl border border-rose-100 px-3 py-3" />
                <button class="w-full rounded-full border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800">Cancel appointment</button>
            </form>
        @else
            <p class="mt-3 text-sm text-stone-600">Cancellation is disabled due to the cutoff window.</p>
        @endif
    </section>

    <section class="rounded-[2rem] border border-rose-100 bg-white p-6 shadow-sm sm:p-8">
        <h2 class="text-2xl font-semibold text-stone-900">Cancellation policy</h2>
        <div class="prose mt-4 max-w-none text-stone-600">
            {!! $cancellationPolicyHtml ?: '<p>Please contact us if you need to cancel or reschedule.</p>' !!}
        </div>
    </section>
</div>
@endsection
