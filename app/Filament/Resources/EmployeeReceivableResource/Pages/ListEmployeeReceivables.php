<?php

namespace App\Filament\Resources\EmployeeReceivableResource\Pages;

use App\Filament\Resources\EmployeeReceivableResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeReceivables extends ListRecords
{
    protected static string $resource = EmployeeReceivableResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
