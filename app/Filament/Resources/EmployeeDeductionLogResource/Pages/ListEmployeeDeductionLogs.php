<?php

namespace App\Filament\Resources\EmployeeDeductionLogResource\Pages;

use App\Filament\Resources\EmployeeDeductionLogResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeDeductionLogs extends ListRecords
{
    protected static string $resource = EmployeeDeductionLogResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
