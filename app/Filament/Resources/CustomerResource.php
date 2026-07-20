<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Salon';
    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('phone')
                ->required()
                ->maxLength(32),

            Forms\Components\TextInput::make('email')
                ->email()
                ->maxLength(255),

            Forms\Components\KeyValue::make('notes')
                ->label('Notes / Tags')
                ->helperText('Add any useful notes or tags for marketing and analysis')
                ->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('appointments_count')
                    ->counts('appointments')
                    ->label('Bookings')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('First seen')
                    ->date('j M Y')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
