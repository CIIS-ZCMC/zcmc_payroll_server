<?php

namespace App\Filament\Resources\ReceivableLogResource\Pages;

use App\Filament\Resources\ReceivableLogResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReceivableLogs extends ListRecords
{
    protected static string $resource = ReceivableLogResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
