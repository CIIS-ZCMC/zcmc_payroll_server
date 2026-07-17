<?php

namespace App\Filament\Resources\EmployeeNightDiffComputationResource\Pages;

use App\Filament\Resources\EmployeeNightDiffComputationResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeNightDiffComputations extends ListRecords
{
    protected static string $resource = EmployeeNightDiffComputationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
