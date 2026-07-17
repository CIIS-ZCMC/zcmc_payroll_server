<?php

namespace App\Filament\Resources\ExcludedEmployeeResource\Pages;

use App\Filament\Resources\ExcludedEmployeeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExcludedEmployee extends EditRecord
{
    protected static string $resource = ExcludedEmployeeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
