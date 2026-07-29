<?php

namespace App\Filament\Resources\EmployeeReceivables\Pages;

use App\Filament\Resources\EmployeeReceivables\Actions\ImportReceivablesAction;
use App\Filament\Resources\EmployeeReceivables\EmployeeReceivableResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeReceivables extends ListRecords
{
    protected static string $resource = EmployeeReceivableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportReceivablesAction::make(),
            CreateAction::make(),
        ];
    }
}
