<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TimeOffResource\Pages;
use App\Models\TimeOff;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TimeOffResource extends Resource
{
    protected static ?string $model = TimeOff::class;
    protected static ?string $navigationIcon = 'heroicon-o-no-symbol';
    protected static ?string $navigationGroup = 'Salon';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('staff_id')->relationship('staff', 'name')->required(),
            Forms\Components\DateTimePicker::make('starts_at')->required()->seconds(false),
            Forms\Components\DateTimePicker::make('ends_at')->required()->seconds(false),
            Forms\Components\TextInput::make('reason')->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('starts_at')->columns([
            Tables\Columns\TextColumn::make('staff.name')->label('Staff'),
            Tables\Columns\TextColumn::make('starts_at')->dateTime('D j M H:i'),
            Tables\Columns\TextColumn::make('ends_at')->dateTime('D j M H:i'),
            Tables\Columns\TextColumn::make('reason'),
        ])->actions([
            Tables\Actions\EditAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTimeOff::route('/'),
            'create' => Pages\CreateTimeOff::route('/create'),
            'edit' => Pages\EditTimeOff::route('/{record}/edit'),
        ];
    }
}
