<?php

namespace App\Filament\Resources\EmployeeTimeRecordResource\Pages;

use App\Filament\Resources\EmployeeTimeRecordResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmployeeTimeRecord extends EditRecord
{
    protected static string $resource = EmployeeTimeRecordResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
