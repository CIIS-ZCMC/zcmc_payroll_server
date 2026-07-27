<?php

namespace App\Filament\Resources\ReceivableGroups\Pages;

use App\Filament\Resources\ReceivableGroups\ReceivableGroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReceivableGroups extends ListRecords
{
    protected static string $resource = ReceivableGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
