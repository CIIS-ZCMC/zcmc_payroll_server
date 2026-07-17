<?php

namespace App\Filament\Resources\EmployeeAdjustmentResource\Pages;

use App\Filament\Resources\EmployeeAdjustmentResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeAdjustments extends ListRecords
{
    protected static string $resource = EmployeeAdjustmentResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
