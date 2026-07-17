<?php

namespace App\Filament\Resources\EmployeePayrollResource\Pages;

use App\Filament\Resources\EmployeePayrollResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployeePayrolls extends ListRecords
{
    protected static string $resource = EmployeePayrollResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
