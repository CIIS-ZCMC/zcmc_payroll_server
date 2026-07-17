<?php

namespace App\Filament\Resources\EmployeeTimeRecordResource\Pages;

use App\Filament\Resources\EmployeeTimeRecordResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeTimeRecords extends ListRecords
{
    protected static string $resource = EmployeeTimeRecordResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
