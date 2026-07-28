<?php

namespace App\Filament\Resources\EmployeeDeductions\Pages;

use App\Filament\Resources\EmployeeDeductions\Actions\ImportDeductionsAction;
use App\Filament\Resources\EmployeeDeductions\EmployeeDeductionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeDeductions extends ListRecords
{
    protected static string $resource = EmployeeDeductionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportDeductionsAction::make(),
            CreateAction::make(),
        ];
    }
}
