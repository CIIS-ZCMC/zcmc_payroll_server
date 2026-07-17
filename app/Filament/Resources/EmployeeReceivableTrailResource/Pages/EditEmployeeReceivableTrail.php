<?php

namespace App\Filament\Resources\EmployeeReceivableTrailResource\Pages;

use App\Filament\Resources\EmployeeReceivableTrailResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmployeeReceivableTrail extends EditRecord
{
    protected static string $resource = EmployeeReceivableTrailResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
