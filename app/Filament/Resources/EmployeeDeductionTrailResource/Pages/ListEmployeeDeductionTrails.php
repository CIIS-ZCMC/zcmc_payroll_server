<?php

namespace App\Filament\Resources\EmployeeDeductionTrailResource\Pages;

use App\Filament\Resources\EmployeeDeductionTrailResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeDeductionTrails extends ListRecords
{
    protected static string $resource = EmployeeDeductionTrailResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
