<?php

namespace App\Filament\Resources\EmployeeReceivableResource\Pages;

use App\Filament\Resources\EmployeeReceivableResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmployeeReceivable extends EditRecord
{
    protected static string $resource = EmployeeReceivableResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
