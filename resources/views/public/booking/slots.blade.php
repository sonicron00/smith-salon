@extends('layouts.public')

@section('content')
<div class="mx-auto max-w-3xl rounded-[2rem] border border-rose-100 bg-white p-6 shadow-sm sm:p-8">
    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-rose-700">Step 3</p>
    <h1 class="mt-3 text-3xl font-semibold text-stone-900">Pick a date and time</h1>
    <p class="mt-2 text-sm leading-7 text-stone-600">{{ $service->name }} with {{ $staff->name }}</p>

    <form class="mt-5 rounded-[1.25rem] bg-stone-50 p-4" method="get">
        <label class="text-sm font-medium text-stone-700">Date</label>
        <input type="date" name="date" value="{{ $date }}" class="mt-2 w-full rounded-xl border border-rose-100 bg-white px-3 py-3" />
        <button class="mt-3 rounded-full border border-rose-200 px-4 py-2 text-sm font-medium text-rose-800">Change date</button>
    </form>

    <div class="mt-6">
        @if (empty($slots))
            <div class="rounded-[1.25rem] bg-stone-50 p-4 text-stone-700">No slots available for this date.</div>
        @else
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                @foreach ($slots as $time)
                    <button
                        class="rounded-[1rem] border border-rose-100 px-3 py-3 text-sm font-medium text-stone-800 transition hover:border-rose-200 hover:bg-rose-50"
                        onclick="document.getElementById('time').value='{{ $time }}'; document.getElementById('selected-time').innerText='{{ $time }}'; document.getElementById('details').scrollIntoView({behavior:'smooth'}); return false;">
                        {{ $time }}
                    </button>
                @endforeach
            </div>
        @endif
    </div>

    <div id="details" class="mt-8 border-t border-rose-100 pt-6">
        <div class="rounded-[1.25rem] bg-stone-50 p-4">
            <div class="text-sm text-stone-600">Selected time</div>
            <div id="selected-time" class="mt-1 text-lg font-semibold text-stone-900">Choose a slot above</div>
        </div>

        <form class="mt-4 space-y-4" method="post" action="{{ route('booking.confirm') }}">
            @csrf
            <input type="hidden" name="service_id" value="{{ $service->id }}" />
            <input type="hidden" name="staff_id" value="{{ $staff->id }}" />
            <input type="hidden" name="date" value="{{ $date }}" />
            <input id="time" type="hidden" name="time" value="" />

            <div>
                <label class="text-sm font-medium text-stone-700">Name</label>
                <input name="customer_name" class="mt-2 w-full rounded-xl border border-rose-100 px-3 py-3" required />
            </div>

            <div>
                <label class="text-sm font-medium text-stone-700">Mobile number</label>
                <input name="customer_phone" class="mt-2 w-full rounded-xl border border-rose-100 px-3 py-3" placeholder="+447..." required />
            </div>

            <div>
                <label class="text-sm font-medium text-stone-700">Email (optional)</label>
                <input name="customer_email" class="mt-2 w-full rounded-xl border border-rose-100 px-3 py-3" type="email" />
            </div>

            <button class="w-full rounded-full bg-stone-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-black">Confirm booking</button>
            <p class="text-xs leading-6 text-stone-500">You’ll receive a text confirmation with a link to manage your appointment.</p>
        </form>
    </div>
</div>
@endsection
