<?php

namespace App\Filament\Resources\EmployeeAdjustmentResource\Pages;

use App\Filament\Resources\EmployeeAdjustmentResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmployeeAdjustment extends EditRecord
{
    protected static string $resource = EmployeeAdjustmentResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
