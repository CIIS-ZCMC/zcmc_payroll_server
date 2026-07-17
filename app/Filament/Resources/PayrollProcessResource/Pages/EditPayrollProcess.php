<?php

namespace App\Filament\Resources\PayrollProcessResource\Pages;

use App\Filament\Resources\PayrollProcessResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPayrollProcess extends EditRecord
{
    protected static string $resource = PayrollProcessResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
