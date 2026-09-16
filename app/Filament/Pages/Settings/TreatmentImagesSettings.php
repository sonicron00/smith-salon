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
        // Don't load blobs into form state — just show current status
        $this->state = [
            'image_one' => null,
            'image_two' => null,
        ];
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            FileUpload::make('image_one')
                ->label('Photo 1 (left)')
                ->image()
                ->disk('local')
                ->directory('tmp-uploads')
                ->maxSize(5120)
                ->helperText(fn () => Setting::get('treatments.image_one')
                    ? 'Currently uploaded — upload a new file to replace it.'
                    : 'No photo uploaded yet.'),

            FileUpload::make('image_two')
                ->label('Photo 2 (right)')
                ->image()
                ->disk('local')
                ->directory('tmp-uploads')
                ->maxSize(5120)
                ->helperText(fn () => Setting::get('treatments.image_two')
                    ? 'Currently uploaded — upload a new file to replace it.'
                    : 'No photo uploaded yet.'),
        ])->statePath('state');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save')
                ->action(function () {
                    $this->saveImage('image_one', 'treatments.image_one');
                    $this->saveImage('image_two', 'treatments.image_two');
                    Notification::make()->title('Photos saved')->success()->send();
                }),
        ];
    }

    private function saveImage(string $field, string $settingKey): void
    {
        $path = $this->state[$field] ?? null;

        if (is_array($path)) {
            $path = reset($path) ?: null;
        }

        if (! $path) {
            return; // No new file uploaded — keep existing
        }

        $fullPath = storage_path('app/local/tmp-uploads/' . basename($path));

        if (! file_exists($fullPath)) {
            $fullPath = storage_path('app/tmp-uploads/' . basename($path));
        }

        if (! file_exists($fullPath)) {
            return;
        }

        $mime = mime_content_type($fullPath);
        $base64 = base64_encode(file_get_contents($fullPath));
        $dataUri = "data:{$mime};base64,{$base64}";

        Setting::put($settingKey, $dataUri);

        // Clean up temp file
        @unlink($fullPath);
    }
}
