<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppointmentResource\Pages;
use App\Models\Appointment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Salon';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('staff_id')->relationship('staff', 'name')->required(),
            Forms\Components\Select::make('service_id')->relationship('service', 'name')->required(),
            Forms\Components\DateTimePicker::make('starts_at')->required()->seconds(false),
            Forms\Components\DateTimePicker::make('ends_at')->required()->seconds(false),
            Forms\Components\TextInput::make('customer_name')->required(),
            Forms\Components\TextInput::make('customer_phone')->required(),
            Forms\Components\TextInput::make('customer_email')->email(),
            Forms\Components\Select::make('status')->options([
                Appointment::STATUS_BOOKED => 'Booked',
                Appointment::STATUS_CANCELLED => 'Cancelled',
                Appointment::STATUS_COMPLETED => 'Completed',
                Appointment::STATUS_NO_SHOW => 'No show',
            ])->required(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('starts_at')
            ->columns([
                Tables\Columns\TextColumn::make('starts_at')->dateTime('D j M H:i')->sortable(),
                Tables\Columns\TextColumn::make('customer_name')->searchable(),
                Tables\Columns\TextColumn::make('service.name')->label('Service')->searchable(),
                Tables\Columns\TextColumn::make('staff.name')->label('Staff')->searchable(),
                Tables\Columns\TextColumn::make('customer_phone')->label('Phone'),
                Tables\Columns\BadgeColumn::make('status')->colors([
                    'success' => Appointment::STATUS_BOOKED,
                    'danger' => Appointment::STATUS_CANCELLED,
                    'gray' => Appointment::STATUS_COMPLETED,
                    'warning' => Appointment::STATUS_NO_SHOW,
                ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('staff')->relationship('staff', 'name'),
                Tables\Filters\SelectFilter::make('status')->options([
                    Appointment::STATUS_BOOKED => 'Booked',
                    Appointment::STATUS_CANCELLED => 'Cancelled',
                    Appointment::STATUS_COMPLETED => 'Completed',
                    Appointment::STATUS_NO_SHOW => 'No show',
                ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}
