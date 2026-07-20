<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationGroup = 'Salon';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),

            Forms\Components\CheckboxList::make('staff')
                ->relationship('staff', 'name')
                ->columns(2)
                ->label('Available with'),

            Forms\Components\TextInput::make('duration_minutes')
                ->numeric()
                ->required()
                ->helperText('Must be a multiple of 30')
                ->rule('multiple_of:30'),

            Forms\Components\TextInput::make('buffer_minutes')
                ->numeric()
                ->default(0)
                ->helperText('Optional buffer between appointments, in multiples of 30')
                ->rule('multiple_of:30'),

            Forms\Components\TextInput::make('price_pence')
                ->numeric()
                ->default(0)
                ->helperText('Stored in pence, e.g. 3500 for £35.00'),

            Forms\Components\TextInput::make('sort_order')
                ->numeric()
                ->default(0)
                ->helperText('Lower numbers appear first on the public booking page'),

            Forms\Components\Toggle::make('active')
                ->default(true),

            Forms\Components\Select::make('consultation_form_id')
                ->relationship('consultationForm', 'name')
                ->label('Consultation form')
                ->helperText('Customers will be asked to complete this form before their appointment')
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('duration_minutes')
                    ->label('Duration')
                    ->sortable(),

                Tables\Columns\IconColumn::make('active')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}