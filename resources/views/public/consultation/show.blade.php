@extends('layouts.public')

@section('content')
<div class="mx-auto max-w-2xl rounded-[2rem] border border-rose-100 bg-white p-6 shadow-sm sm:p-8">
    <h1 class="text-3xl font-semibold text-stone-900">Consultation Form</h1>
    <p class="mt-2 text-sm leading-7 text-stone-600">
        {{ $form->name }} — for your {{ $appointment->service->name }} appointment on
        <span class="font-semibold">{{ $appointment->starts_at->format('D j M Y \a\t H:i') }}</span>
    </p>

    @if (session('status'))
        <div class="mt-4 rounded-[1rem] border border-green-200 bg-green-50 p-4 text-sm text-green-800">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mt-4 rounded-[1rem] border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @if ($existingResponse)
        <div class="mt-6 rounded-[1.25rem] bg-stone-50 p-4">
            <div class="font-medium text-stone-900">You've already completed this form. Thank you!</div>
            <p class="mt-2 text-sm text-stone-600">Your answers have been saved and your therapist will review them before your appointment.</p>
        </div>
    @else
        <form class="mt-6 space-y-5" method="post" action="{{ route('appointment.consultation.submit', ['token' => $appointment->manage_token]) }}">
            @csrf

            @foreach ($form->fields as $index => $field)
                <div>
                    <label class="text-sm font-medium text-stone-700">
                        {{ $field['label'] }}
                        @if ($field['required'] ?? false)
                            <span class="text-red-500">*</span>
                        @endif
                    </label>

                    @if ($field['type'] === 'text')
                        <input
                            name="answers[{{ $index }}]"
                            type="text"
                            value="{{ old("answers.{$index}") }}"
                            class="mt-2 w-full rounded-xl border border-rose-100 px-3 py-3"
                            {{ ($field['required'] ?? false) ? 'required' : '' }}
                        />
                    @elseif ($field['type'] === 'date')
                        <input
                            name="answers[{{ $index }}]"
                            type="date"
                            value="{{ old("answers.{$index}") }}"
                            class="mt-2 w-full rounded-xl border border-rose-100 px-3 py-3"
                            {{ ($field['required'] ?? false) ? 'required' : '' }}
                        />
                    @elseif ($field['type'] === 'textarea')
                        <textarea
                            name="answers[{{ $index }}]"
                            rows="3"
                            class="mt-2 w-full rounded-xl border border-rose-100 px-3 py-3"
                            {{ ($field['required'] ?? false) ? 'required' : '' }}
                        >{{ old("answers.{$index}") }}</textarea>
                    @elseif ($field['type'] === 'select')
                        <select
                            name="answers[{{ $index }}]"
                            class="mt-2 w-full rounded-xl border border-rose-100 px-3 py-3"
                            {{ ($field['required'] ?? false) ? 'required' : '' }}
                            onchange="toggleOther(this, {{ $index }})"
                        >
                            <option value="">— Select —</option>
                            @foreach ($field['options'] ?? [] as $option)
                                <option value="{{ $option }}" {{ old("answers.{$index}") === $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                        @if (!empty($field['other_options']))
                            <input
                                name="answers_other[{{ $index }}]"
                                type="text"
                                id="other-{{ $index }}"
                                value="{{ old("answers_other.{$index}") }}"
                                placeholder="Please specify..."
                                class="mt-2 w-full rounded-xl border border-rose-100 px-3 py-3 hidden"
                                data-other-options="{{ implode(',', $field['other_options']) }}"
                            />
                        @endif
                    @elseif ($field['type'] === 'radio')
                        <div class="mt-2 space-y-2">
                            @foreach ($field['options'] ?? [] as $option)
                                <label class="flex items-center gap-2 text-sm text-stone-700">
                                    <input type="radio" name="answers[{{ $index }}]" value="{{ $option }}" {{ old("answers.{$index}") === $option ? 'checked' : '' }} onchange="toggleOther(this, {{ $index }})" />
                                    {{ $option }}
                                </label>
                            @endforeach
                        </div>
                        @if (!empty($field['other_options']))
                            <input
                                name="answers_other[{{ $index }}]"
                                type="text"
                                id="other-{{ $index }}"
                                value="{{ old("answers_other.{$index}") }}"
                                placeholder="Please specify..."
                                class="mt-2 w-full rounded-xl border border-rose-100 px-3 py-3 hidden"
                                data-other-options="{{ implode(',', $field['other_options']) }}"
                            />
                        @endif
                    @elseif ($field['type'] === 'checkbox')
                        <div class="mt-2">
                            <input type="hidden" name="answers[{{ $index }}]" value="0" />
                            <label class="flex items-center gap-2 text-sm text-stone-700">
                                <input type="checkbox" name="answers[{{ $index }}]" value="1" {{ old("answers.{$index}") ? 'checked' : '' }} />
                                Yes
                            </label>
                        </div>
                    @endif
                </div>
            @endforeach

            <button class="w-full rounded-full bg-stone-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-black">
                Submit form
            </button>
        </form>

        <script>
            function toggleOther(el, index) {
                const otherInput = document.getElementById('other-' + index);
                if (!otherInput) return;

                const otherOptions = otherInput.dataset.otherOptions.split(',');
                const selectedValue = el.type === 'radio' ? el.value : el.value;

                if (otherOptions.includes(selectedValue)) {
                    otherInput.classList.remove('hidden');
                } else {
                    otherInput.classList.add('hidden');
                    otherInput.value = '';
                }
            }
        </script>
    @endif
</div>
@endsection
