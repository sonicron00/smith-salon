<?php

namespace App\Filament\Pages\Settings;

use App\Models\Setting;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class SocialSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-share';
    protected static string $view = 'filament.pages.settings.social';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $title = 'Social Media';

    public array $state = [];

    public function mount(): void
    {
        $this->state = [
            'facebook_url' => Setting::get('social.facebook_url'),
            'instagram_url' => Setting::get('social.instagram_url'),
        ];
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('facebook_url')
                ->label('Facebook page URL')
                ->url()
                ->placeholder('https://www.facebook.com/yourpage'),

            TextInput::make('instagram_url')
                ->label('Instagram profile URL')
                ->url()
                ->placeholder('https://www.instagram.com/yourhandle'),
        ])->statePath('state');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save')
                ->action(function () {
                    Setting::put('social.facebook_url', $this->state['facebook_url'] ?? null);
                    Setting::put('social.instagram_url', $this->state['instagram_url'] ?? null);
                    Notification::make()->title('Saved')->success()->send();
                }),
        ];
    }
}
