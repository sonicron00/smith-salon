<?php

namespace App\Filament\Pages;

use App\Models\Appointment;
use Filament\Forms;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class AppointmentCalendarWidget extends FullCalendarWidget
{
    public string|null|\Illuminate\Database\Eloquent\Model $model = Appointment::class;

    public function fetchEvents(array $info): array
    {
        return Appointment::query()
            ->whereIn('status', [Appointment::STATUS_BOOKED, Appointment::STATUS_COMPLETED])
            ->where('starts_at', '>=', $info['start'])
            ->where('ends_at', '<=', $info['end'])
            ->with(['service', 'staff'])
            ->get()
            ->map(function (Appointment $appointment) {
                return [
                    'id' => $appointment->id,
                    'title' => $appointment->customer_name . ' — ' . ($appointment->service?->name ?? ''),
                    'start' => $appointment->starts_at->toIso8601String(),
                    'end' => $appointment->ends_at->toIso8601String(),
                    'color' => $this->getColorForStaff($appointment->staff_id),
                ];
            })
            ->toArray();
    }

    public function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('customer_name')
                ->label('Customer')
                ->disabled(),

            Forms\Components\TextInput::make('customer_phone')
                ->label('Phone')
                ->disabled(),

            Forms\Components\TextInput::make('service_name')
                ->label('Service')
                ->disabled()
                ->formatStateUsing(fn ($record) => $record?->service?->name),

            Forms\Components\TextInput::make('staff_name')
                ->label('Staff')
                ->disabled()
                ->formatStateUsing(fn ($record) => $record?->staff?->name),

            Forms\Components\TextInput::make('starts_at')
                ->label('Time')
                ->disabled()
                ->formatStateUsing(fn ($record) => $record?->starts_at?->format('D j M Y H:i')),

            Forms\Components\TextInput::make('status')
                ->disabled(),
        ];
    }

    public function config(): array
    {
        return [
            'initialView' => 'timeGridWeek',
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek,timeGridDay',
            ],
            'slotMinTime' => '08:00:00',
            'slotMaxTime' => '21:00:00',
            'allDaySlot' => false,
            'slotDuration' => '00:30:00',
            'nowIndicator' => true,
            'editable' => false,
            'selectable' => false,
        ];
    }

    protected function headerActions(): array
    {
        return [];
    }

    protected function modalActions(): array
    {
        return [];
    }

    private function getColorForStaff(int $staffId): string
    {
        $colors = [
            '#e11d48', // rose-600
            '#7c3aed', // violet-600
            '#2563eb', // blue-600
            '#059669', // emerald-600
            '#d97706', // amber-600
            '#dc2626', // red-600
        ];

        return $colors[$staffId % count($colors)];
    }
}
