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
    protected static ?string $navigationLabel = 'Blocked times';
    protected static ?string $modelLabel = 'blocked time';
    protected static ?string $pluralModelLabel = 'blocked times';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('staff_id')
                ->relationship('staff', 'name')
                ->label('Therapist')
                ->required(),

            Forms\Components\DateTimePicker::make('starts_at')
                ->label('Blocked from')
                ->required()
                ->seconds(false),

            Forms\Components\DateTimePicker::make('ends_at')
                ->label('Blocked until')
                ->required()
                ->seconds(false)
                ->rule('after:starts_at'),

            Forms\Components\TextInput::make('reason')
                ->label('Note')
                ->placeholder('Holiday, lunch, appointment, training...')
                ->maxLength(255),

            Forms\Components\Toggle::make('is_recurring')
                ->label('Recurring weekly')
                ->helperText('Block repeats on the same weekday and time every week until turned off')
                ->default(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('starts_at')
            ->columns([
                Tables\Columns\TextColumn::make('staff.name')
                    ->label('Therapist'),

                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Blocked from')
                    ->dateTime('D j M H:i'),

                Tables\Columns\TextColumn::make('ends_at')
                    ->label('Blocked until')
                    ->dateTime('D j M H:i'),

                Tables\Columns\TextColumn::make('reason')
                    ->label('Note'),

                Tables\Columns\IconColumn::make('is_recurring')
                    ->label('Recurring')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->label('Unblock'),
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