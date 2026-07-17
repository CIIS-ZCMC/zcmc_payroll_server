<?php

namespace App\Filament\Resources\EmployeeNightDiffComputationResource\Pages;

use App\Filament\Resources\EmployeeNightDiffComputationResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmployeeNightDiffComputation extends EditRecord
{
    protected static string $resource = EmployeeNightDiffComputationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
