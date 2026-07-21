<?php

namespace App\Filament\Pages;

use App\Filament\Resources\AppointmentResource;
use App\Models\Appointment;
use Filament\Pages\Page;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class Calendar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Calendar';
    protected static ?string $title = 'Appointment Calendar';
    protected static ?int $navigationSort = 0;
    protected static string $view = 'filament.pages.calendar';

    protected function getHeaderWidgets(): array
    {
        return [
            AppointmentCalendarWidget::class,
        ];
    }
}
