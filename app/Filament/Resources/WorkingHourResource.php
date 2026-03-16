<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkingHourResource\Pages;
use App\Models\WorkingHour;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WorkingHourResource extends Resource
{
    protected static ?string $model = WorkingHour::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Salon';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('staff_id')->relationship('staff', 'name')->required(),
            Forms\Components\Select::make('weekday')->options([
                0 => 'Monday', 1 => 'Tuesday', 2 => 'Wednesday', 3 => 'Thursday', 4 => 'Friday', 5 => 'Saturday', 6 => 'Sunday',
            ])->required(),
            Forms\Components\TimePicker::make('start_time')->seconds(false)->required(),
            Forms\Components\TimePicker::make('end_time')->seconds(false)->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('staff.name')->label('Staff'),
            Tables\Columns\TextColumn::make('weekday')->formatStateUsing(fn ($state) => ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'][$state] ?? $state),
            Tables\Columns\TextColumn::make('start_time'),
            Tables\Columns\TextColumn::make('end_time'),
        ])->actions([
            Tables\Actions\EditAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkingHours::route('/'),
            'create' => Pages\CreateWorkingHour::route('/create'),
            'edit' => Pages\EditWorkingHour::route('/{record}/edit'),
        ];
    }
}
