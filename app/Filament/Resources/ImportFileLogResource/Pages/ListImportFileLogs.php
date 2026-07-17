<?php

namespace App\Filament\Resources\ImportFileLogResource\Pages;

use App\Filament\Resources\ImportFileLogResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListImportFileLogs extends ListRecords
{
    protected static string $resource = ImportFileLogResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
