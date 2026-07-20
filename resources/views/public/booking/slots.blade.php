@extends('layouts.public')

@section('content')
    <div class="mx-auto max-w-3xl rounded-[2rem] border border-rose-100 bg-white p-6 shadow-sm sm:p-8">
        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-rose-700">Step 3</p>
        <h1 class="mt-3 text-3xl font-semibold text-stone-900">Pick a date and time</h1>
        <p class="mt-2 text-sm leading-7 text-stone-600">{{ $service->name }} with {{ $staff->name }}</p>

        @if ($errors->any())
            <div class="mt-4 rounded-[1rem] border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <section class="mt-6 rounded-[1.5rem] border border-rose-100 bg-stone-50 p-4">
            <div class="flex items-center justify-between gap-3">
                @if ($calendar['previous_month'])
                    <a
                            href="{{ route('booking.slots', ['service' => $service->id, 'staff' => $staff->id, 'month' => $calendar['previous_month'], 'date' => $calendar['previous_month'] . '-01']) }}"
                            class="rounded-full border border-rose-200 bg-white px-3 py-1 text-sm font-medium text-rose-800"
                    >
                        ←
                    </a>
                @else
                    <span class="px-3 py-1"></span>
                @endif

                <div class="text-sm font-semibold text-stone-900">{{ $calendar['label'] }}</div>

                @if ($calendar['next_month'])
                    <a
                            href="{{ route('booking.slots', ['service' => $service->id, 'staff' => $staff->id, 'month' => $calendar['next_month'], 'date' => $calendar['next_month'] . '-01']) }}"
                            class="rounded-full border border-rose-200 bg-white px-3 py-1 text-sm font-medium text-rose-800"
                    >
                        →
                    </a>
                @else
                    <span class="px-3 py-1"></span>
                @endif
            </div>

            <div class="mt-4 grid grid-cols-7 gap-1 text-center text-xs font-semibold uppercase tracking-wide text-stone-500">
                <div>Mon</div>
                <div>Tue</div>
                <div>Wed</div>
                <div>Thu</div>
                <div>Fri</div>
                <div>Sat</div>
                <div>Sun</div>
            </div>

            <div class="mt-2 space-y-1">
                @foreach ($calendar['weeks'] as $week)
                    <div class="grid grid-cols-7 gap-1">
                        @foreach ($week as $day)
                            @if ($day['is_current_month'] && $day['is_available'])
                                <a
                                        href="{{ route('booking.slots', ['service' => $service->id, 'staff' => $staff->id, 'date' => $day['date'], 'month' => $calendar['month']]) }}"
                                        class="rounded-xl border px-2 py-2 text-center text-sm font-semibold transition
                                    {{ $day['is_selected']
                                        ? 'border-stone-900 bg-stone-900 text-white'
                                        : 'border-rose-200 bg-white text-rose-900 hover:bg-rose-50' }}"
                                >
                                    {{ $day['day'] }}
                                </a>
                            @else
                                <span
                                        class="rounded-xl px-2 py-2 text-center text-sm
                                    {{ $day['is_current_month'] ? 'text-stone-300' : 'text-stone-200' }}"
                                >
                                {{ $day['day'] }}
                            </span>
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </div>

            <p class="mt-3 text-xs leading-5 text-stone-500">
                Highlighted dates have available appointments. Use the arrows to check another month.
            </p>
        </section>

        <form class="mt-4" method="get" id="date-form">
            <input type="hidden" name="month" value="{{ $calendar['month'] }}" />

            <label class="text-sm font-medium text-stone-700">Or choose a date manually</label>
            <input
                    type="date"
                    name="date"
                    value="{{ $date }}"
                    min="{{ now()->addHours(24)->format('Y-m-d') }}"
                    max="{{ now()->addWeeks(6)->format('Y-m-d') }}"
                    class="mt-2 w-full rounded-xl border border-rose-100 px-3 py-3"
                    onchange="this.form.submit()"
            />
        </form>

        <div class="mt-6">
            @if (empty($slots))
                <div class="rounded-[1.25rem] bg-stone-50 p-4 text-stone-700">
                    <div class="font-medium text-stone-900">No slots available for this date.</div>

                    @if ($nextAvailableSlot)
                        <div class="mt-2 text-sm leading-6">
                            Next available slot:
                            <a
                                    href="{{ route('booking.slots', ['service' => $service->id, 'staff' => $staff->id, 'date' => $nextAvailableSlot['date'], 'month' => $nextAvailableSlot['month']]) }}"
                                    class="font-semibold text-rose-800 underline"
                            >
                                {{ $nextAvailableSlot['label'] }}
                            </a>
                        </div>
                    @else
                        <div class="mt-2 text-sm leading-6">
                            No upcoming slots were found. Please try another therapist or treatment.
                        </div>
                    @endif

                    <p class="mt-3 text-sm leading-6 text-stone-600">
                        If you don't see a booking that suits please message us and we'll do our best to accommodate you.
                    </p>
                </div>
            @else
                @foreach ($slotGroups as $period => $periodSlots)
                    @if (empty($periodSlots))
                        @continue
                    @endif

                    <div class="{{ $loop->first ? '' : 'mt-6' }}">
                        <h2 class="text-sm font-semibold uppercase tracking-[0.25em] text-rose-700">
                            {{ $period === 'AM' ? 'AM' : 'PM' }}
                        </h2>

                        <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-4">
                            @foreach ($periodSlots as $time)
                                <button
                                        type="button"
                                        class="rounded-[1rem] border border-rose-100 px-3 py-3 text-sm font-medium text-stone-800 transition hover:border-rose-200 hover:bg-rose-50"
                                        onclick="selectSlot('{{ $time }}')"
                                >
                                    {{ $time }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        @if (! empty($slots))
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
                    <input id="time" type="hidden" name="time" value="{{ old('time') }}" />

                    <div>
                        <label class="text-sm font-medium text-stone-700">Name</label>
                        <input
                                name="customer_name"
                                value="{{ old('customer_name') }}"
                                class="mt-2 w-full rounded-xl border border-rose-100 px-3 py-3"
                                required
                        />
                    </div>

                    <div>
                        <label class="text-sm font-medium text-stone-700">Mobile number</label>
                        <input
                                name="customer_phone"
                                value="{{ old('customer_phone') }}"
                                class="mt-2 w-full rounded-xl border border-rose-100 px-3 py-3"
                                placeholder="+447..."
                                required
                        />
                    </div>

                    <div>
                        <label class="text-sm font-medium text-stone-700">Email (optional)</label>
                        <input
                                name="customer_email"
                                value="{{ old('customer_email') }}"
                                class="mt-2 w-full rounded-xl border border-rose-100 px-3 py-3"
                                type="email"
                        />
                    </div>

                    <div>
                        <label class="text-sm font-medium text-stone-700">Message (optional)</label>
                        <textarea
                                name="customer_message"
                                rows="3"
                                class="mt-2 w-full rounded-xl border border-rose-100 px-3 py-3"
                                placeholder="Add anything you would like us to know before your appointment."
                        >{{ old('customer_message') }}</textarea>
                    </div>

                    <button class="w-full rounded-full bg-stone-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-black">
                        Confirm booking
                    </button>

                    <p class="text-xs leading-6 text-stone-500">
                        You’ll receive a text confirmation with a link to manage your appointment.
                    </p>
                </form>
            </div>
        @endif
    </div>

    <script>
        function selectSlot(time) {
            document.getElementById('time').value = time;
            document.getElementById('selected-time').innerText = time;
            document.getElementById('details').scrollIntoView({ behavior: 'smooth' });
        }
    </script>
@endsection