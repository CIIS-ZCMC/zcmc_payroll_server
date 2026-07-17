<?php

namespace App\Filament\Resources\EmployeeDeductionResource\Pages;

use App\Filament\Resources\EmployeeDeductionResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeDeductions extends ListRecords
{
    protected static string $resource = EmployeeDeductionResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
