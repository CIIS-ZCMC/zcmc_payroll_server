<?php

namespace App\Filament\Resources\EmployeeReceivableLogResource\Pages;

use App\Filament\Resources\EmployeeReceivableLogResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeReceivableLogs extends ListRecords
{
    protected static string $resource = EmployeeReceivableLogResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
