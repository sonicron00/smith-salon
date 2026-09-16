<?php

namespace App\Filament\Pages\Settings;

use App\Models\Setting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class TreatmentImagesSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static string $view = 'filament.pages.settings.treatment-images';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $title = 'Treatment Photos';

    public array $state = [];

    public function mount(): void
    {
        $this->state = [
            'image_one' => Setting::get('treatments.image_one'),
            'image_two' => Setting::get('treatments.image_two'),
        ];
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            FileUpload::make('image_one')
                ->label('Photo 1 (left)')
                ->image()
                ->directory('treatments')
                ->disk('public')
                ->imageEditor()
                ->maxSize(5120)
                ->helperText('Shown on the left of the Treatments page.'),

            FileUpload::make('image_two')
                ->label('Photo 2 (right)')
                ->image()
                ->directory('treatments')
                ->disk('public')
                ->imageEditor()
                ->maxSize(5120)
                ->helperText('Shown on the right of the Treatments page.'),
        ])->statePath('state');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save')
                ->action(function () {
                    Setting::put('treatments.image_one', $this->normalisePath($this->state['image_one'] ?? null));
                    Setting::put('treatments.image_two', $this->normalisePath($this->state['image_two'] ?? null));
                    Notification::make()->title('Saved')->success()->send();
                }),
        ];
    }

    /**
     * Filament FileUpload state can be an array (temp uploads) or a string (existing path).
     * Normalise to a single stored path string.
     */
    private function normalisePath($value): ?string
    {
        if (is_array($value)) {
            $value = reset($value) ?: null;
        }

        return $value ?: null;
    }
}
