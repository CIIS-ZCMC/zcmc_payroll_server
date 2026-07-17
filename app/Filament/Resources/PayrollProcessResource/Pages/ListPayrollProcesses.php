<?php

namespace App\Filament\Resources\PayrollProcessResource\Pages;

use App\Filament\Resources\PayrollProcessResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPayrollProcesses extends ListRecords
{
    protected static string $resource = PayrollProcessResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
