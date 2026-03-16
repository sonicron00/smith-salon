<?php

namespace App\Filament\Pages\Settings;

use App\Models\Setting;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class PolicySettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.settings.policy';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $title = 'Policies';

    public array $state = [];

    public function mount(): void
    {
        $this->state = [
            'cancellation_html' => Setting::get('policy.cancellation_html'),
        ];
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            RichEditor::make('cancellation_html')
                ->label('Cancellation policy')
                ->required(),
        ])->statePath('state');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save')
                ->action(function () {
                    Setting::put('policy.cancellation_html', $this->state['cancellation_html'] ?? null);
                    Notification::make()->title('Saved')->success()->send();
                }),
        ];
    }
}
