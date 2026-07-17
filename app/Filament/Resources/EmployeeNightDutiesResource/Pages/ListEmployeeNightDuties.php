<?php

namespace App\Filament\Resources\EmployeeNightDutiesResource\Pages;

use App\Filament\Resources\EmployeeNightDutiesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeNightDuties extends ListRecords
{
    protected static string $resource = EmployeeNightDutiesResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
