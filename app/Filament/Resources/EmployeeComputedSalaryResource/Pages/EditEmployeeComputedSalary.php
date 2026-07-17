<?php

namespace App\Filament\Resources\EmployeeComputedSalaryResource\Pages;

use App\Filament\Resources\EmployeeComputedSalaryResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmployeeComputedSalary extends EditRecord
{
    protected static string $resource = EmployeeComputedSalaryResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
