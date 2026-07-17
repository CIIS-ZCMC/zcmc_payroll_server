<?php

namespace App\Filament\Resources\EmployeeDeductionLogResource\Pages;

use App\Filament\Resources\EmployeeDeductionLogResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmployeeDeductionLog extends EditRecord
{
    protected static string $resource = EmployeeDeductionLogResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
