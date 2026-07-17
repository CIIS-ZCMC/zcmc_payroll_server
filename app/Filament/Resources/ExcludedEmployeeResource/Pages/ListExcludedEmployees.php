<?php

namespace App\Filament\Resources\ExcludedEmployeeResource\Pages;

use App\Filament\Resources\ExcludedEmployeeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExcludedEmployees extends ListRecords
{
    protected static string $resource = ExcludedEmployeeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
