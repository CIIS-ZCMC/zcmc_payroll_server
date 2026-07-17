<?php

namespace App\Filament\Resources\EmployeeComputedSalaryResource\Pages;

use App\Filament\Resources\EmployeeComputedSalaryResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeComputedSalaries extends ListRecords
{
    protected static string $resource = EmployeeComputedSalaryResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
