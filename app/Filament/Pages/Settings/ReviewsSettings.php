<?php

namespace App\Filament\Pages\Settings;

use App\Models\Setting;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ReviewsSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static string $view = 'filament.pages.settings.reviews';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $title = 'Reviews';

    public array $state = [];

    public function mount(): void
    {
        $this->state = [
            'reviews_embed_html' => Setting::get('reviews.embed_html'),
        ];
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Textarea::make('reviews_embed_html')
                ->label('Reviews widget embed code')
                ->rows(8)
                ->helperText('Paste the full embed code from your reviews provider (e.g. Trustindex or Elfsight). Leave blank to hide the reviews section.'),
        ])->statePath('state');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save')
                ->action(function () {
                    Setting::put('reviews.embed_html', $this->state['reviews_embed_html'] ?? null);
                    Notification::make()->title('Saved')->success()->send();
                }),
        ];
    }
}
