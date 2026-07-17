<?php

namespace App\Filament\Resources\PayrollSummaryResource\Pages;

use App\Filament\Resources\PayrollSummaryResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPayrollSummaries extends ListRecords
{
    protected static string $resource = PayrollSummaryResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
