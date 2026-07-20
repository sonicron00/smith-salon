<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\ConsultationResponse;
use Illuminate\Http\Request;

class ConsultationFormController extends Controller
{
    public function show(string $token)
    {
        $appointment = Appointment::query()
            ->where('manage_token', $token)
            ->where('status', Appointment::STATUS_BOOKED)
            ->firstOrFail();

        $form = $appointment->service->consultationForm;

        if (! $form) {
            abort(404, 'No consultation form is required for this appointment.');
        }

        // If already filled out, show a thank-you message
        $existingResponse = $appointment->consultationResponse;

        return view('public.consultation.show', compact('appointment', 'form', 'existingResponse'));
    }

    public function submit(string $token, Request $request)
    {
        $appointment = Appointment::query()
            ->where('manage_token', $token)
            ->where('status', Appointment::STATUS_BOOKED)
            ->firstOrFail();

        $form = $appointment->service->consultationForm;

        if (! $form) {
            abort(404);
        }

        // Don't allow re-submission
        if ($appointment->consultationResponse()->exists()) {
            return back()->with('status', 'You have already completed this form. Thank you!');
        }

        // Build validation rules from form fields
        $rules = [];
        foreach ($form->fields as $index => $field) {
            $key = "answers.{$index}";
            $fieldRules = [];

            if ($field['required'] ?? false) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            if (in_array($field['type'], ['text', 'textarea', 'radio'])) {
                $fieldRules[] = 'string';
                $fieldRules[] = 'max:2000';
            }

            if ($field['type'] === 'select') {
                $fieldRules[] = 'string';
            }

            if ($field['type'] === 'checkbox') {
                $fieldRules[] = 'boolean';
            }

            $rules[$key] = $fieldRules;
        }

        $validated = $request->validate($rules);

        ConsultationResponse::create([
            'appointment_id' => $appointment->id,
            'consultation_form_id' => $form->id,
            'answers' => $validated['answers'] ?? [],
        ]);

        return back()->with('status', 'Thank you! Your consultation form has been submitted.');
    }
}
