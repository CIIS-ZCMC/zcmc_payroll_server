<?php

namespace App\Filament\Resources\EmployeeReceivableTrailResource\Pages;

use App\Filament\Resources\EmployeeReceivableTrailResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeReceivableTrails extends ListRecords
{
    protected static string $resource = EmployeeReceivableTrailResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
