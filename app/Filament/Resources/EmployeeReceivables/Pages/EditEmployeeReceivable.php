<?php

namespace App\Filament\Resources\EmployeeReceivables\Pages;

use App\Filament\Resources\EmployeeReceivables\Actions\CompleteReceivableAction;
use App\Filament\Resources\EmployeeReceivables\Actions\StopReceivableAction;
use App\Filament\Resources\EmployeeReceivables\EmployeeReceivableResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEmployeeReceivable extends EditRecord
{
    protected static string $resource = EmployeeReceivableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            StopReceivableAction::make(),
            CompleteReceivableAction::make(),
            DeleteAction::make(),
        ];
    }
}
