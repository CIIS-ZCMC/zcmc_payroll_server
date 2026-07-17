<?php

namespace App\Filament\Resources\ImportFileLogResource\Pages;

use App\Filament\Resources\ImportFileLogResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditImportFileLog extends EditRecord
{
    protected static string $resource = ImportFileLogResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
