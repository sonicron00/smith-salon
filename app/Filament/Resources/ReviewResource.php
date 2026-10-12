<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;
    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationGroup = 'Salon';
    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('author')
                ->required()
                ->maxLength(255),

            Forms\Components\Select::make('rating')
                ->required()
                ->default(5)
                ->options([
                    5 => '5 stars',
                    4 => '4 stars',
                    3 => '3 stars',
                    2 => '2 stars',
                    1 => '1 star',
                ]),

            Forms\Components\Textarea::make('text')
                ->label('Review text')
                ->required()
                ->rows(4)
                ->columnSpanFull(),

            Forms\Components\TextInput::make('relative_time')
                ->label('When (display text)')
                ->placeholder('e.g. 2 weeks ago')
                ->helperText('Optional. Free text shown under the author name.')
                ->maxLength(255),

            Forms\Components\Toggle::make('is_visible')
                ->label('Show on website')
                ->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('author')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('rating')
                    ->formatStateUsing(fn ($state) => str_repeat('★', (int) $state))
                    ->color('warning'),

                Tables\Columns\TextColumn::make('text')
                    ->limit(50)
                    ->wrap(),

                Tables\Columns\BadgeColumn::make('source')
                    ->colors([
                        'primary' => 'google',
                        'gray' => 'manual',
                    ]),

                Tables\Columns\IconColumn::make('is_visible')
                    ->label('Visible')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('source')
                    ->options([
                        'google' => 'Google',
                        'manual' => 'Manual',
                    ]),
                Tables\Filters\TernaryFilter::make('is_visible')
                    ->label('Visibility'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
