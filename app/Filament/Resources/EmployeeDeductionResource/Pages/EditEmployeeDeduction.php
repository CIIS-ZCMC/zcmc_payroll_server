<?php

namespace App\Filament\Resources\EmployeeDeductionResource\Pages;

use App\Filament\Resources\EmployeeDeductionResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmployeeDeduction extends EditRecord
{
    protected static string $resource = EmployeeDeductionResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
