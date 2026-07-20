<?php

namespace App\Filament\Resources\ConsultationFormResource\Pages;

use App\Filament\Resources\ConsultationFormResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConsultationForm extends EditRecord
{
    protected static string $resource = ConsultationFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
