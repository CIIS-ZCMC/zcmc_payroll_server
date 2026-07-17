<?php

namespace App\Filament\Resources\ImportFilesResource\Pages;

use App\Filament\Resources\ImportFilesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditImportFiles extends EditRecord
{
    protected static string $resource = ImportFilesResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
