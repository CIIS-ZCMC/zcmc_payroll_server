<?php

namespace App\Filament\Resources\ImportFilesResource\Pages;

use App\Filament\Resources\ImportFilesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListImportFiles extends ListRecords
{
    protected static string $resource = ImportFilesResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
