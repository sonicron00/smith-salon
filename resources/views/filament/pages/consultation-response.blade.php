<div class="space-y-4">
    @if ($appointment->consultationResponse && $appointment->consultationResponse->form)
        @php
            $form = $appointment->consultationResponse->form;
            $answers = $appointment->consultationResponse->answers;
        @endphp

        <div class="text-sm text-gray-500">
            Form: {{ $form->name }} · Submitted {{ $appointment->consultationResponse->created_at->format('D j M Y H:i') }}
        </div>

        <div class="divide-y divide-gray-100">
            @foreach ($form->fields as $index => $field)
                <div class="py-3">
                    <div class="text-sm font-medium text-gray-700">{{ $field['label'] }}</div>
                    <div class="mt-1 text-sm text-gray-900">
                        @php
                            $answer = $answers[$index] ?? null;
                        @endphp

                        @if ($field['type'] === 'checkbox')
                            {{ $answer ? 'Yes' : 'No' }}
                        @elseif ($answer)
                            {{ $answer }}
                        @else
                            <span class="text-gray-400 italic">Not answered</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-sm text-gray-500">No consultation form response available.</p>
    @endif
</div>
