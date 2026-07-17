<?php

namespace App\Filament\Resources\StoppageLogResource\Pages;

use App\Filament\Resources\StoppageLogResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStoppageLogs extends ListRecords
{
    protected static string $resource = StoppageLogResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
