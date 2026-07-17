<?php

namespace App\Filament\Resources\EmployeeDeductionTrailResource\Pages;

use App\Filament\Resources\EmployeeDeductionTrailResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmployeeDeductionTrail extends EditRecord
{
    protected static string $resource = EmployeeDeductionTrailResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
