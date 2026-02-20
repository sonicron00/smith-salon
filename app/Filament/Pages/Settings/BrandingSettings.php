<?php

namespace App\Filament\Pages\Settings;

use App\Models\Setting;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;

class BrandingSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static string $view = 'filament.pages.settings.branding';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $title = 'Branding';

    public array $state = [];

    public function mount(): void
    {
        $this->state = [
            'instagram_profile_url' => Setting::get('instagram.profile_url'),
            'instagram_embed_html'  => Setting::get('instagram.embed_html'),
        ];
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('instagram_profile_url')
                ->label('Instagram profile URL')
                ->placeholder('https://www.instagram.com/your-salon/')
                ->url(),
            Textarea::make('instagram_embed_html')
                ->label('Instagram embed HTML (optional)')
                ->rows(6)
                ->helperText('Paste a feed widget embed snippet from your chosen provider.'),
        ])->statePath('state');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save')
                ->action(function () {
                    Setting::put('instagram.profile_url', $this->state['instagram_profile_url'] ?? null);
                    Setting::put('instagram.embed_html', $this->state['instagram_embed_html'] ?? null);
                    $this->notify('success', 'Saved.');
                }),
        ];
    }
}
