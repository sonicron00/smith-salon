@extends('layouts.public')

@section('content')
<div class="rounded-2xl bg-white p-6 shadow-sm">
    <h1 class="text-xl font-semibold">Pick a time</h1>
    <p class="mt-1 text-gray-600">{{ $service->name }} with {{ $staff->name }}</p>

    <form class="mt-4" method="get">
        <label class="text-sm text-gray-700">Date</label>
        <input type="date" name="date" value="{{ $date }}" class="mt-1 w-full rounded-xl border px-3 py-2" />
        <button class="mt-3 rounded-xl border px-4 py-2">Change date</button>
    </form>

    <div class="mt-6">
        @if (empty($slots))
            <div class="rounded-xl bg-gray-50 p-4 text-gray-700">No slots available for this date.</div>
        @else
            <div class="grid grid-cols-3 gap-2 sm:grid-cols-4">
                @foreach ($slots as $time)
                    <button
                        class="rounded-xl border px-3 py-2 text-sm hover:bg-gray-50"
                        onclick="document.getElementById('time').value='{{ $time }}'; document.getElementById('details').scrollIntoView({behavior:'smooth'}); return false;">
                        {{ $time }}
                    </button>
                @endforeach
            </div>
        @endif
    </div>

    <div id="details" class="mt-8 border-t pt-6">
        <h2 class="text-lg font-semibold">Your details</h2>

        <form class="mt-3 space-y-3" method="post" action="{{ route('booking.confirm') }}">
            @csrf
            <input type="hidden" name="service_id" value="{{ $service->id }}" />
            <input type="hidden" name="staff_id" value="{{ $staff->id }}" />
            <input type="hidden" name="date" value="{{ $date }}" />
            <input id="time" type="hidden" name="time" value="" />

            <div>
                <label class="text-sm text-gray-700">Name</label>
                <input name="customer_name" class="mt-1 w-full rounded-xl border px-3 py-2" required />
            </div>

            <div>
                <label class="text-sm text-gray-700">Mobile number</label>
                <input name="customer_phone" class="mt-1 w-full rounded-xl border px-3 py-2" placeholder="+447..." required />
            </div>

            <div>
                <label class="text-sm text-gray-700">Email (optional)</label>
                <input name="customer_email" class="mt-1 w-full rounded-xl border px-3 py-2" type="email" />
            </div>

            <button class="w-full rounded-xl bg-black px-4 py-3 text-white">Confirm booking</button>
            <p class="text-xs text-gray-500">You’ll receive a text confirmation with a link to manage your appointment.</p>
        </form>
    </div>
</div>
@endsection
