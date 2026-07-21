<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConsultationFormResource\Pages;
use App\Models\ConsultationForm;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ConsultationFormResource extends Resource
{
    protected static ?string $model = ConsultationForm::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Salon';
    protected static ?string $navigationLabel = 'Consultation forms';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->helperText('Internal name for this form (e.g. "Head Spa Consultation")'),

            Forms\Components\Toggle::make('active')
                ->default(true),

            Forms\Components\Repeater::make('fields')
                ->label('Form fields')
                ->schema([
                    Forms\Components\TextInput::make('label')
                        ->required()
                        ->helperText('The question or field label shown to the customer'),

                    Forms\Components\Select::make('type')
                        ->required()
                        ->live()
                        ->options([
                            'text' => 'Short text',
                            'textarea' => 'Long text',
                            'date' => 'Date',
                            'select' => 'Dropdown',
                            'checkbox' => 'Checkbox (yes/no)',
                            'radio' => 'Multiple choice',
                        ]),

                    Forms\Components\TagsInput::make('options')
                        ->helperText('Options for dropdown or multiple choice fields')
                        ->visible(fn (Forms\Get $get) => in_array($get('type'), ['select', 'radio'])),

                    Forms\Components\TagsInput::make('other_options')
                        ->label('Options that show a text field')
                        ->helperText('If the customer picks one of these options, a free-text box appears (e.g. "Other")')
                        ->visible(fn (Forms\Get $get) => in_array($get('type'), ['select', 'radio'])),

                    Forms\Components\Toggle::make('required')
                        ->default(false),
                ])
                ->columns(1)
                ->defaultItems(1)
                ->addActionLabel('Add field')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('fields')
                    ->label('Fields')
                    ->formatStateUsing(fn ($record) => count($record->fields ?? []) . ' fields'),

                Tables\Columns\IconColumn::make('active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('services_count')
                    ->counts('services')
                    ->label('Linked services'),
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
            'index' => Pages\ListConsultationForms::route('/'),
            'create' => Pages\CreateConsultationForm::route('/create'),
            'edit' => Pages\EditConsultationForm::route('/{record}/edit'),
        ];
    }
}
