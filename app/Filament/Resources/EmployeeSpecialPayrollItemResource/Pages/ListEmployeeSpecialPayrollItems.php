<?php

namespace App\Filament\Resources\EmployeeSpecialPayrollItemResource\Pages;

use App\Filament\Resources\EmployeeSpecialPayrollItemResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeSpecialPayrollItems extends ListRecords
{
    protected static string $resource = EmployeeSpecialPayrollItemResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
