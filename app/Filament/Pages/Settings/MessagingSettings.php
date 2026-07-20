<?php

namespace App\Filament\Pages\Settings;

use App\Models\Setting;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class MessagingSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static string $view = 'filament.pages.settings.messaging';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $title = 'Messaging';

    public array $state = [];

    public function mount(): void
    {
        $this->state = [
            'confirmation_template' => Setting::get('sms.confirmation_template'),
            'reminder_template' => Setting::get('sms.reminder_template'),
            'reminder_lead_hours' => (int) (Setting::get('sms.reminder_lead_hours') ?? 24),
        ];
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Textarea::make('confirmation_template')
                ->label('Confirmation SMS template')
                ->rows(4)
                ->helperText('Tokens: {{name}}, {{service}}, {{staff}}, {{date}}, {{time}}, {{manage_url}}, {{consultation_url}}')
                ->required(),
            Textarea::make('reminder_template')
                ->label('Reminder SMS template')
                ->rows(4)
                ->helperText('Tokens: {{name}}, {{service}}, {{staff}}, {{date}}, {{time}}, {{manage_url}}, {{consultation_url}}')
                ->required(),
            \Filament\Forms\Components\TextInput::make('reminder_lead_hours')
                ->label('Send reminder how many hours before appointment?')
                ->numeric()
                ->default(24)
                ->required()
                ->helperText('e.g. 24 = one day before, 48 = two days before'),
        ])->statePath('state');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save')
                ->action(function () {
                    Setting::put('sms.confirmation_template', $this->state['confirmation_template'] ?? null);
                    Setting::put('sms.reminder_template', $this->state['reminder_template'] ?? null);
                    Setting::put('sms.reminder_lead_hours', $this->state['reminder_lead_hours'] ?? '24');
                    Notification::make()->title('Saved')->success()->send();
                }),
        ];
    }
}
