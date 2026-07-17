<?php

namespace App\Filament\Resources\EmployeeReceivableLogResource\Pages;

use App\Filament\Resources\EmployeeReceivableLogResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmployeeReceivableLog extends EditRecord
{
    protected static string $resource = EmployeeReceivableLogResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
